# trainModel.py
# Attempt to train regression model to predict score on post

import pandas
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.feature_extraction import DictVectorizer
from scipy.sparse import hstack
from sklearn.linear_model import Ridge



slist = ['nba', 'cfb', 'nfl', 'baseball', 'hockey', 'todayilearned', 'science', 'gaming', 'lifeprotips', 'showerthoughts', 'askreddit', 'leagueoflegends', 'programming', 'cscareerquestions', 'youtubehaiku', 'guitar', 'writingprompts', 'tennis', 'dataisbeautiful', 'funny', 'pics', 'aww', 'ama', 'iama', 'jokes', 'dadjokes', 'diy', 'mildlyinteresting', 'tifu', 'memes', 'dankmemes']

for sub in slist:
    # Get the data
    train = pandas.read_csv(('./train_test_csv/train_%s.csv' % sub), delimiter='`', header=None)
    #test = pandas.read_csv(('./train_test_csv/test_%s.csv' % sub), delimiter='`', header=None)

    train[0].str.lower()
    #test[0].str.lower()


    train[0].replace('[^a-zA-Z0-9]', ' ', regex = True)
    #test[0].replace('[^a-zA-Z0-9]', ' ', regex = True)

    vectorizer = TfidfVectorizer(min_df=5)

    X_train = vectorizer.fit_transform(train[0].values.astype('U'))

    X = hstack([X_train])

    clf = Ridge(alpha=1.0, random_state=241)

    y = train[1]

    # train the model
    clf.fit(X, y)
    
    # save the model and vectorizer
    filename = ('./models/model_%s.pkl' % sub)
    pickle.dump(clf, open(filename, 'wb'))

    filename = ('./models/vect_%s.pkl' % sub)
    pickle.dump(vectorizer, open(filename, 'wb'))

