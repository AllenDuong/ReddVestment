# -*- coding: utf-8 -*-
"""
Created on Thu Oct 25 15:32:19 2018

@author: ptfis
"""

import requests
import json
import MySQLdb

headers = {'user-agent': 'reddit-{}'.format('pfische1')}


def insertPost(subreddit, created, p_id, author, title, url, is_self, gilded):
    try:
        pass
        #TODO: Create database and put all the stuff in here
        conn = MySQLdb.connect(host='temp', user='temp', passwd='123', db='posts')
    except:
        print('Cannot connect to post database')
        return 0
    print('Connected to post database')

    cursor = conn.cursor()
    cursor.execute('INSERT INTO posts '
                   + 'VALUES(%s, %f, %s, %s, %s, %s, %r, %d);'
                   % (subreddit, created, p_id, author, title, url, is_self, gilded))
    conn.close()
        

def scrapeNewFromSubreddit(subreddit):
    REDDIT_URL = 'https://www.reddit.com/r/%s/new.json?limit=50' % subreddit
    response = requests.get(REDDIT_URL, headers=headers)
    r_data = json.loads(response.text)
    for item in r_data['data']['children']:
        print('--------------')
        item = item['data']
        subreddit = item['subreddit']
        created = item['created']
        p_id = item['id']
        author = item['author']
        title = item['title']
        url = item['url']
        is_self = item['is_self']
        gilded = item['gilded']
        print('Subreddit: ' + subreddit)
        print('Created: ' + str(created))
        print('ID: ' + p_id)
        print('Author: ' + author)
        print('Title: ' + title)
        print('URL: ' + url)
        print('is_self: ' + str(is_self))
        print('gilded: ' + str(gilded))
        insertPost(subreddit, created, p_id, author, title, url, is_self, gilded)
        print('--------------')
        
if __name__ == '__main__':
    scrapeNewFromSubreddit('NBA')