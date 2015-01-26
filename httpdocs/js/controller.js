var REFRESH_RATE = 1000;
var COOLDOWN_LENGTH = 100;

var app = angular.module('app',[]);

app.controller('ChainGangController', ['$scope', '$http', '$interval', '$timeout',
    function($scope, $http, $interval, $timeout) {
  $scope.input = '';
  $scope.chain = ['ace', 'bar', 'car'];

  var readChain = function(params) {
    var url = 'http://5tephen.com/chaingang/db.php?action=readchain';
    params.count = 25;
    var read = $http.get(url, {
      params: params
    })
    read.success(function(data) {
      $scope.chain = data.chain;
    });
  };

  var readChainSince = function(link) {
    readChain({
      since: link.id,
      cachebust: (new Date()).getTime()
    });
  };

  var rand255 = function() {
    return Math.round(Math.random() * 255);
  }

  $scope.rerollRandomRGB = function() {
    $scope.randRGB = [
      rand255(),
      rand255(),
      rand255()
    ].join(",");
  }
  $scope.rerollRandomRGB();

  $interval(function() {
    readChainSince($scope.chain[0]);
  }, REFRESH_RATE);

  $scope.coolingDown = false;
  var startCooldown = function() {
    $scope.coolingDown = true;
    $timeout(endCooldown, COOLDOWN_LENGTH);
  };

  var endCooldown = function() {
    $scope.coolingDown = false;

  };

  $scope.submit = function(newPhrase) {
    if ($scope.coolingDown) {
      return;
    }

    startCooldown();

    var url = 'http://5tephen.com/chaingang/db.php?action=addLink';
    var params = {
      newPhrase: newPhrase,
      currentPhrase: $scope.chain[0].phrase,
      color: $scope.randRGB
    };

    var request = $http.get(url, {
      params: params
    });

    $scope.input = '';
  };
}]);
