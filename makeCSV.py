import requests
import json
import MySQLdb
import time
import csv

conn = MySQLdb.connect(host='localhost', user='tblazek', passwd='goirish', db='tblazek')

slist = ['nba', 'cfb', 'nfl', 'baseball', 'hockey', 'todayilearned', 'science', 'gaming', 'lifeprotips', 'showerthoughts', 'askreddit', 'leagueoflegends', 'programming', 'cscareerquestions', 'youtubehaiku', 'guitar', 'writingprompts', 'tennis', 'dataisbeautiful', 'funny', 'pics', 'aww', 'ama', 'iama', 'jokes', 'dadjokes', 'diy', 'mildlyinteresting', 'tifu', 'memes', 'dankmemes']

# Delete all training and testing data
cursor = conn.cursor()
command = ('DELETE FROM TrainData WHERE id IS NOT NULL;')
cursor.execute(command)
conn.commit()

command = ('DELETE FROM TestData WHERE id IS NOT NULL;')
cursor.execute(command)
conn.commit()

# make training and testing data
for subreddit in slist:
    # Get testing data
    cursor = conn.cursor()
    command = ('INSERT INTO TestData (SELECT * FROM Posts WHERE subreddit=\'%s\' AND final_score IS NOT NULL AND RAND() < 0.1);' % subreddit)
    command = command.encode('ascii', errors='ignore')
    cursor.execute(command)
    conn.commit()
    
    # Get training data
    cursor = conn.cursor()
    command = ('INSERT INTO TrainData (SELECT * FROM Posts WHERE subreddit=\'%s\' AND final_score IS NOT NULL AND id NOT IN (SELECT id FROM TrainData));' % subreddit)
    command = command.encode('ascii', errors='ignore')
    cursor.execute(command)
    conn.commit()
    

for subreddit in slist:
    cursor = conn.cursor()
    command = ('SELECT title, final_score FROM TrainData WHERE subreddit=\'%s\';' % subreddit)
    command = command.encode('ascii', errors='ignore')
    cursor.execute(command)
    conn.commit()
    result = cursor.fetchall()

    title = ('./train_test_csv/train_%s.csv' % subreddit)
    fp = open(title, 'w')
    c = csv.writer(fp, delimiter='`')
    for row in result:
        row[0].replace('`','\'')
        c.writerow(row)

    fp.close()

    cursor = conn.cursor()
    command = ('SELECT title, final_score FROM TestData WHERE subreddit=\'%s\';' % subreddit)
    command = command.encode('ascii', errors='ignore')
    cursor.execute(command)
    conn.commit()
    result = cursor.fetchall()

    title = ('./train_test_csv/test_%s.csv' % subreddit)
    fp = open(title, 'w')
    c = csv.writer(fp, delimiter='`')
    for row in result:
        row[0].replace('`','\'')
        c.writerow(row)

    fp.close()


