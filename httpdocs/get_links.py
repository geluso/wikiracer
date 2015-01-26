import argparse
from bs4 import BeautifulSoup
from sets import Set

def get_links(filename):
  html = open(filename).read()
  soup = BeautifulSoup(html)

  links = Set([])

  for tag in soup.findAll('a', href=True):
      href = tag['href']
      if ("cite_ref" in href):
        continue
      elif ("cite_note" in href):
        continue
      else:
        links.add(href)
  return links


def main():
  # set up parser
  parser = argparse.ArgumentParser(description="prints links in wikipedia article")
  parser.add_argument('filepath', help="path to file containing article's HTML")

  # run parser
  args = parser.parse_args()

  # get all links from HTML file
  links = get_links(args.filepath)

  for link in links:
    print link

if __name__ == "__main__":
  main()
