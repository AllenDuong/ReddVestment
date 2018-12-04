# -*- coding: utf-8 -*-
"""
Created on Thu Oct 25 15:32:19 2018

@author: ptfis
"""

import requests
import json
import MySQLdb
import time
import pandas
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.feature_extraction import DictVectorizer
from scipy.sparse import hstack
from sklearn.linear_model import Ridge

# Headers for Reddit API request
headers = {'user-agent': 'reddit-{}'.format('pfische1')}


def insertPost(subreddit, created, p_id, author, title, url, score, is_self, gilded, thumbnail, first_comments, predicted_score):
    # Insert a post into the database
    try:
        # Try to connect to the database
        conn = MySQLdb.connect(host='localhost', user='tblazek', passwd='goirish', db='tblazek')
    except:
        print('Cannot connect to post database')
        return 1

    cursor = conn.cursor()
    command = ('INSERT IGNORE INTO Posts '
                   + 'VALUES(\'%s\', %d, \'%s\', \'%s\', \'%s\', \'%s\', %d, %i, %d, NULL, NULL, \'%s\', NULL, NULL, %d, NULL, NULL, NULL, %d);'
                   % (subreddit, int(created), p_id, author, title, url, score, is_self, gilded, thumbnail, first_comments, predicted_score))
    command = command.encode('ascii', errors='ignore')

    try:
        cursor.execute(command)
        conn.commit()
    except Exception as e:
        print("Exception occured when inserting into mysql")
        print("-- Full command --")
        print(command)
        print("-- end of command --")
        print("error message")
        print(str(e))
    conn.close()
        

def scrapeNewFromSubreddit(subreddit):
    # Scrape 100 most recent posts form specified subreddit
    
    modelname = ('./models/model_%s.pkl' % subreddit)
    predict_model = pickle.load(open(modelname, 'rb'))
    vectorizer = pickle.load(open(('./models/vect_%s.pkl' % subreddit), 'rb'))
    
    try:
        REDDIT_URL = 'https://www.reddit.com/r/%s/new.json?limit=100' % subreddit
        response = requests.get(REDDIT_URL, headers=headers)
        r_data = json.loads(response.text)
    except Exception as e:
        print("exception occured scrape from new")
        print(e)
        return 1

    for item in r_data['data']['children']:
        #print('--------------')
        item = item['data']
        subreddit = item['subreddit'].replace('\'', '\'').replace('\"', '\"')
        created = item['created_utc']
        p_id = item['id'].replace('\'', "''").replace('\"', '""')
        author = item['author'].replace('\'', "''").replace('\"', '""')
        title = item['title'].replace('\'', "''").replace('\"', '""')
        url = item['url'].replace("\'", "''").replace('\"', '""')
        score = item['score']
        is_self = item['is_self']
        gilded = item['gilded']
        thumbnail = item['thumbnail']
        first_comments = item['num_comments']
        
        # Only insert post if it is less than 10 minutes old. This way we can start and
        # stop the scraper without worry
        if time.time() - created <= 600:
            # format data needed for prediction
            title = title.encode('ascii', errors='ignore')
            
            # create pandas dataframe for predictor
            d = [title]
            X_title = pandas.DataFrame(data=d)
            X_title[0].str.lower()
            X_title[0].replace('[^a-zA-Z0-9]', ' ', regex = True)
            X_test = vectorizer.transform(X_title[0].values.astype('U'))
            X_test = hstack([X_test])
            
            # get the prediction
            result = predict_model.predict(X_test)
            
            # insert post into database
            insertPost(subreddit, created, p_id, author, title, url, score, is_self, gilded, thumbnail, first_comments, int(result))
        else:   # Posted over 10 minutes ago
            break


def updatePosts():
    # Update second score on posts that are older than two hours
    try:
        # Connect to database
        conn = MySQLdb.connect(host='localhost', user='tblazek', passwd='goirish', db='tblazek')
    except:
        print('Cannot connect to post database')
        return 0

    UPDATE_URL = 'https://www.reddit.com/by_id/'
    command = 'SELECT * FROM Posts WHERE second_score IS NULL;'
    cursor = conn.cursor()
    cursor.execute(command)
    results = cursor.fetchall()
    # If post is older than two hours, get new
    for row in results:
        post_time = row[1]
        p_id = row[2]
        if time.time() - post_time >= 7200 and time.time() - post_time <= 7800:
            # If post is older than two hours update the second_score field
            try:
                response = requests.get(UPDATE_URL+'t3_'+p_id+'.json', headers=headers)
                r_data = json.loads(response.text)
                pi = r_data['data']['children'][0]
                new_score = pi['data']['score']
                second_comments = pi['data']['num_comments']
                second_gilding = pi['data']['gilded']
                update_command = 'UPDATE Posts SET second_score=%d, second_comments=%d, second_gilding=%d WHERE id=\'%s\'' % (new_score, second_comments, second_gilding, p_id)
                cursor.execute(update_command)
                conn.commit()
            except Exception as e:
                print("Exception occured update 2 hours")
                print(e)
    
    command = 'SELECT * FROM Posts WHERE third_score IS NULL;'
    cursor.execute(command)
    results = cursor.fetchall()
    for row in results:
        post_time = row[1]
        p_id = row[2]
        if time.time() - post_time >= 43200 and time.time() - post_time <= 43800:
            # If post is older than twelve hours update the second_score field
            try:
                response = requests.get(UPDATE_URL+'t3_'+p_id+'.json', headers=headers)
                r_data = json.loads(response.text)
                pi = r_data['data']['children'][0]
                new_score = pi['data']['score']
                third_comments = pi['data']['num_comments']
                third_gilding = pi['data']['gilded']
                update_command = 'UPDATE Posts SET third_score=%d, third_comments=%d, third_gilding=%d, final_score=(%d + (%d * 1000) + (%d*0.5)) WHERE id=\'%s\'' % (new_score, third_comments, third_gilding, new_score, third_gilding, third_comments, p_id)
                cursor.execute(update_command)
                conn.commit()
            except Exception as e:
                print("Exception Occured update 12 hours")
                print(e)

    conn.close()


if __name__ == '__main__':
    subList = ['nba', 'cfb', 'nfl', 'baseball', 'hockey', 'todayilearned', 'science', 'gaming', 'lifeprotips', 'showerthoughts', 'askreddit', 'leagueoflegends', 'programming', 'cscareerquestions', 'youtubehaiku', 'guitar', 'writingprompts', 'tennis', 'dataisbeautiful', 'funny', 'pics', 'aww', 'ama', 'iama', 'jokes', 'dadjokes', 'diy', 'mildlyinteresting', 'tifu', 'memes', 'dankmemes']
    while True:
        print('scraping')
        for subreddit in subList:
            scrapeNewFromSubreddit(subreddit)
        print('done scraping')
        print('updating')
        updatePosts()
        print('done updating')
        time.sleep(60)
