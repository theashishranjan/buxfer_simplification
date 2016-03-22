<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.5/angular.min.js"></script>
    <style type="text/css">
        .tg  {border-collapse:collapse;border-spacing:0;border-color:#999;}
        .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#444;background-color:#F7FDFA;border-top-width:1px;border-bottom-width:1px;}
        .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;border-top-width:1px;border-bottom-width:1px;}
    </style>
</head>

<body ng-app="myapp">

    <div ng-controller="MyController" >
        <center>
            <button ng-click="myData.doClick(item, $event)" ng-hide="myData.fromServer.status" style="font: bold large times new roman,sans-serif,helvetica;">Show minimized Transaction</button>
            <div ng-show="loading" class="loading"><img src="loader2.gif">Loading....</div>
        </center>
        <br/>
        <div ng-show="myData.fromServer.status">
            <h2> Minimized transactions among registered users</h2><span style="float:right"><a href="register.php">Link to Register</a></span>
            <div ng-repeat="(giver, transaction) in myData.fromServer.optimized">
                <table style="width=100%" class="tg">
                <col width="240">
                <col width="60">
                <col width="240">
                <col width="60">
                    <tr ng-repeat="(taker, amount) in transaction">
                        <td class="tg-031e"><b>{{ giver }}</b></td>
                        <td class="tg-031e">owes</td>
                        <td class="tg-031e"><b>{{ taker }}</b></td>
                        <td class="tg-031e"><b>{{ amount }}</b></td>
                    </tr>
                </table>
                <br />
            </div>

            <h2>Transactions with other users</h2>

            <div ng-repeat="(giver, transaction) in myData.fromServer.left">
                <table style="width=100%" class="tg">
                <col width="240">
                <col width="100">
                <col width="240">
                <col width="60">
                    <tr ng-repeat="(taker, amount) in transaction" ng-if="amount < 0">
                        <td class="tg-031e"><b>{{ giver }}</b></td>
                        <td class="tg-031e"><font color="red">owes</font></td>
                        <td class="tg-031e"><b>{{ taker }}</b></td>
                        <td class="tg-031e"><b><font color="red">{{ amount * -1 }}</font></b></td>
                    </tr>
                    <tr ng-repeat="(taker, amount) in transaction" ng-if="amount > 0">
                        <td class="tg-031e"><b>{{ giver }}</b></td>
                        <td class="tg-031e"><font color="green">receives from</font></td>
                        <td class="tg-031e"><b>{{ taker }}</b></td>
                        <td class="tg-031e"><b><font color="green">{{ amount }}</font></b></td>
                    </tr>
                </table>
                <br />
            </div>
        </div>
    </div>

    <script>
    angular.module("myapp", [])
        .controller("MyController", function($scope, $http) {
            $scope.myData = {};
            $scope.myData.doClick = function(item, event) {
                $scope.loading = true;
                var responsePromise = $http.get("processData.php");

                responsePromise.success(function(data, status, headers, config) {
                    $scope.myData.fromServer = data;
                    $scope.loading = false;
                });
                responsePromise.error(function(data, status, headers, config) {
                    alert("Kuch toh thukaa!!");
                });
            };
        });
    </script>

</body>

</html>
