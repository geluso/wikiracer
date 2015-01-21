from BeautifulSoup import BeautifulSoup
from os import listdir
from sets import Set
from itertools import combinations

def get_hrefs(files, directory):
  all_sets = []
  for filename in files:
    print "reading", filename
    path = directory + filename
    html = open(path).read()
    soup = BeautifulSoup(html)

    new_set = Set([])
    all_sets.append(new_set)

    for tag in soup.findAll('a', href=True):
        href = tag['href']
        new_set.add(href)
  return all_sets

def get_common_href(sets):
  common_href = sets[0]
  for s in sets:
    common_href = common_href & s
  return common_href

def main():
  directory = "./html/"
  files = listdir(directory)
  hrefs = get_hrefs(files, directory)

  for (href, filename) in zip(hrefs, files):
    print "%04d from %s" % (len(href), filename)
  print

  for crange in range(2, len(hrefs) + 1):
    print "crange:", crange
    for c in combinations(hrefs, crange):
      common = get_common_href(c)

      if (len(common) == 40):
        for href in common:
          print href

      print "%d common" % (len(common))
    print

main()
