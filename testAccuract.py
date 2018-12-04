
slist = ['nba', 'cfb', 'nfl', 'baseball', 'hockey', 'todayilearned', 'science', 'gaming', 'lifeprotips', 'showerthoughts', 'askreddit', 'leagueoflegends', 'programming', 'cscareerquestions', 'youtubehaiku', 'guitar', 'writingprompts', 'tennis', 'dataisbeautiful', 'funny', 'pics', 'aww', 'ama', 'iama', 'jokes', 'dadjokes', 'diy', 'mildlyinteresting', 'tifu', 'memes', 'dankmemes']

for sub in slist:
    info = open(('./train_test_csv/test_%s.csv' % sub), 'r')
    results = open(('./results/result_%s.txt' % sub), 'r')
    total_diff = 0
    lines = 0
    for line in info:
        lines += 1
        line = line.split('`')
        r = results.readline()
        try:
            diff = float(float(r) - float(line[1]))
        except ValueError as e:
            pass
            #print('line', line);
            #print('r', r);
            #print('line[1]', line[1])
        total_diff += abs(diff)
        #print(float(line[1]), float(r), diff)

    print(('Avg diff for %s: ' % sub) + str(total_diff / lines))
    results.close()
    info.close()
