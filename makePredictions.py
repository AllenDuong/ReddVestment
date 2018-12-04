# makePredictions.py
# Attempt to use regression model to predict score on post

import pandas
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.feature_extraction import DictVectorizer
from scipy.sparse import hstack
from sklearn.linear_model import Ridge



slist = ['nba', 'cfb', 'nfl', 'baseball', 'hockey', 'todayilearned', 'science', 'gaming', 'lifeprotips', 'showerthoughts', 'askreddit', 'leagueoflegends', 'programming', 'cscareerquestions', 'youtubehaiku', 'guitar', 'writingprompts', 'tennis', 'dataisbeautiful', 'funny', 'pics', 'aww', 'ama', 'iama', 'jokes', 'dadjokes', 'diy', 'mildlyinteresting', 'tifu', 'memes', 'dankmemes']

for sub in slist:
    # Get the data
    test = pandas.read_csv(('./train_test_csv/test_%s.csv' % sub), delimiter='`', header=None)

    test[0].str.lower()

    test[0].replace('[^a-zA-Z0-9]', ' ', regex = True)

    # load model
    loaded_model = pickle.load(open(('./models/model_%s.pkl' % sub), 'rb'))
    
    #vectorizer = TfidfVectorizer(min_df=5)
    vectorizer = pickle.load(open(('./models/vect_%s.pkl' % sub), 'rb'))
    X_test = vectorizer.transform(test[0].values.astype('U'))
    X_test = hstack([X_test])

    result = loaded_model.predict(X_test)

    with open(('./results/result_%s.txt' % sub), 'w') as results: 
        for item in result:
            results.write(str(item) + '\n')
