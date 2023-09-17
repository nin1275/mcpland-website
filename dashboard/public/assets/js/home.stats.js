function updateChart() {
  var $dashboardChart, $chart, $dataJSON;
  $chart = $('[data-toggle="chart"]');
  $dataJSON = $chart.eq(0).attr("data-update");
  if (typeof $dataJSON === "undefined") {
    $dataJSON = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
  }
  else {
    $dataJSON = jQuery.parseJSON($dataJSON).data.datasets[0].data;
  }
  var a, t = $("#dashboardChart");
  t.length && (a = t, $dashboardChart = new Chart(a, {
    type: "line",
    options: {
      scales: {
        yAxes: [{
          gridLines: {
            color: ThemeCharts.colors.gray[900],
            zeroLineColor: ThemeCharts.colors.gray[900]
          },
          ticks: {
            callback: function(a) {
              if (!(a % 10)) return a
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function(a, $dashboardChart) {
            var t = $dashboardChart.datasets[a.datasetIndex].label || "",
            o = a.yLabel,
            r = "";
            return 1 < $dashboardChart.datasets.length && (r += '<span class="popover-body-label mr-auto">' + t + "</span>"), r += '<span class="popover-body-value">' + o + "</span>"
          }
        }
      }
    },
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Stats",
        data: $dataJSON
      }]
    }
  }), a.data("chart", $dashboardChart));
}

$(document).ready(function() {
  // Account Cache
  var accountCacheChecker = $.ajax({
    type: "GET",
    url: "/apps/dashboard/public/ajax/stats.php?target=check&action=account-cache",
    success: function(result) {
      return result;
    }
  });

  // Chart
  var $dashboardChart = $("#dashboardChart");
  if ($dashboardChart.length) {
    $earnChartData = $("#earnChartData");
    $earnChartValue = $("#earnChartValue");
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=chart&action=earn",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        $earnChartData.removeClass("disabledlink");
        $earnChartData.find(".spinner-border").css("display", "none");
        $earnChartData.attr('data-update', '{"data":{"datasets":[{"data":[' + result.yearEarnedMoneyData + ']}]}}');
        $earnChartValue.find(".spinner-border").css("display", "none");
        $earnChartValue.find("h3").html(result.yearEarnedMoneyValue + '<small class="ml-2">'+currency+'</small>').css("display", "block");
      }
    }).then(function() {
      $userChartData = $("#userChartData");
      $userChartValue = $("#userChartValue");
      accountCacheChecker.done(function(data) {
        $.ajax({
          type: "GET",
          url: "/apps/dashboard/public/ajax/stats.php?target=chart&action=user",
          success: function(result) {
            var result = jQuery.parseJSON(result);
            $userChartData.removeClass("disabledlink");
            $userChartData.find(".spinner-border").css("display", "none");
            $userChartData.attr('data-update', '{"data":{"datasets":[{"data":[' + result.yearRegisteredUsersData + ']}]}}');
            $userChartValue.find(".spinner-border").css("display", "none");
            $userChartValue.find("h3").text(result.yearRegisteredUsersValue).css("display", "block");
          }
        });
      });
    }).then(function() {
      $storeChartData = $("#storeChartData");
      $storeChartValue = $("#storeChartValue");
      $.ajax({
        type: "GET",
        url: "/apps/dashboard/public/ajax/stats.php?target=chart&action=store",
        success: function(result) {
          var result = jQuery.parseJSON(result);
          $storeChartData.removeClass("disabledlink");
          $storeChartData.find(".spinner-border").css("display", "none");
          $storeChartData.attr('data-update', '{"data":{"datasets":[{"data":[' + result.yearStoreHistoryData + ']}]}}');
          $storeChartValue.find(".spinner-border").css("display", "none");
          $storeChartValue.find("h3").text(result.yearStoreHistoryValue).css("display", "block");
        }
      });
    }).then(function() {
      updateChart();
    });
  }

  // Card
  $userCardData = $("#userCardData");
  if ($userCardData.length) {
    accountCacheChecker.done(function(data) {
      $.ajax({
        type: "GET",
        url: "/apps/dashboard/public/ajax/stats.php?target=card&action=user",
        success: function(result) {
          var result = jQuery.parseJSON(result);
          var calculate = Math.floor(((100*(result.thisMonthAccountCount-result.lastMonthAccountCount)) / (Math.max(1, result.lastMonthAccountCount))));
          var badgeClass = (calculate > 0) ? 'badge badge-soft-success mt--1' : (calculate < 0) ? 'badge badge-soft-danger mt--1' : (calculate == 0) ? 'badge badge-soft-secondary mt--1' : null;
          $userCardData.find(".spinner-border").css("display", "none");
          $userCardData.find(".h2").text(result.totalAccountCount).css("display", "inline-block");
          $userCardData.find(".badge").attr('class', badgeClass).text(calculate + '%').css("display", "inline-block");
        }
      });
    });
  }

  $thisMonthEarnData = $("#thisMonthEarnData");
  if ($thisMonthEarnData.length) {
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=card&action=this-month-earn",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        var calculate = Math.floor(((100*(result.thisMonthEarnedMoney-result.lastMonthEarnedMoney)) / (Math.max(1, result.lastMonthEarnedMoney))));
        var badgeClass = (calculate > 0) ? 'badge badge-soft-success mt--1' : (calculate < 0) ? 'badge badge-soft-danger mt--1' : (calculate == 0) ? 'badge badge-soft-secondary mt--1' : null;
        $thisMonthEarnData.find(".spinner-border").css("display", "none");
        $thisMonthEarnData.find(".h2").html(result.thisMonthEarnedMoney + '<small class="ml-2">' + currency + '</small>').css("display", "inline-block");
        $thisMonthEarnData.find(".badge").attr('class', badgeClass).text(calculate + '%').css("display", "inline-block");
      }
    });
  }

  $waitingCommentsCardData = $("#waitingCommentsCardData");
  if ($waitingCommentsCardData.length) {
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=card&action=waiting-comments",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        $waitingCommentsCardData.find(".spinner-border").css("display", "none");
        $waitingCommentsCardData.find(".h2").text(result.rowCount).css("display", "inline-block");
      }
    });
  }

  $thisMonthNewsCardData = $("#thisMonthNewsCardData");
  if ($thisMonthNewsCardData.length) {
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=card&action=this-month-news",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        $thisMonthNewsCardData.find(".spinner-border").css("display", "none");
        $thisMonthNewsCardData.find(".h2").text(result.rowCount).css("display", "inline-block");
      }
    });
  }

  $thisMonthSupportCardData = $("#thisMonthSupportCardData");
  if ($thisMonthSupportCardData.length) {
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=card&action=this-month-support",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        $thisMonthSupportCardData.find(".spinner-border").css("display", "none");
        $thisMonthSupportCardData.find(".h2").text(result.rowCount).css("display", "inline-block");
      }
    });
  }

  $waitingSupportCardData = $("#waitingSupportCardData");
  if ($waitingSupportCardData.length) {
    $.ajax({
      type: "GET",
      url: "/apps/dashboard/public/ajax/stats.php?target=card&action=waiting-support",
      success: function(result) {
        var result = jQuery.parseJSON(result);
        $waitingSupportCardData.find(".spinner-border").css("display", "none");
        $waitingSupportCardData.find(".h2").text(result.rowCount).css("display", "inline-block");
      }
    });
  }
});
