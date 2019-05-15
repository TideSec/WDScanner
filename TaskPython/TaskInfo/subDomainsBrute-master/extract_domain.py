file = open('all.txt','r')

domain = file.readlines()
obuff = []

for a in domain:
    b = a.rstrip()+'\n'

    if b in obuff:
        continue
    obuff.append(b)

with open('out3.txt', 'a+') as handle:
    handle.writelines(obuff)

