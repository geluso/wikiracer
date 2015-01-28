var REFRESH_RATE = 1000;
var COOLDOWN_LENGTH = 100;

var app = angular.module('app',[]);

app.controller('WikiRacerController', ['$scope', '$http',
    function($scope, $http) {
  $scope.currentArticle = {
    href: "/wiki/Abraham_Lincoln",
    title: "Abraham_Lincoln"
  };

  $scope.loading = true;
  $scope.error = false;

  $scope.chosen = [];
  $scope.links = [];


  $scope.chooseArticle = function(article) {
    $scope.currentArticle = article;
    $scope.getLinks(article);
  };

  $scope.getLinks = function(article) {
    $scope.loading = true;

    $scope.chosen.push(article);

    var url = "http://5tephen.com/wikiracer/get_links.php";
    var url = url + "?article=" + article.href.replace("/wiki/", "");

    var request = $http.get(url);

    request.success(function(data) {
      if (data.length > 0 && data.indexOf("\n") != -1) {
        data = data.split("\n");
        $scope.links = _.map(data, makeLink);
      } else {
        $scope.error = "error fetching links. showing links for " + $scope.currentArticle.title;
        $scope.currentArticle = $scope.chosen[$scope.chosen.length - 1];
      }

      $scope.loading = false;
    });
  };

  var makeLink = function(href) {
    var title = href.replace("/wiki/", "");
    title = title.replace(/_/g, " ");

    var link = {
      title: title,
      href: href
    };

    return link;
  };

  var init = function() {
    $scope.chooseArticle($scope.currentArticle);
  };

  init();

}]);
