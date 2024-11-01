<!--
    Copyright 2013 Scott Herbert  (email : scott.a.herbert@googlemail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301
USA

This code has been adapted from google pie chart demo. the orginal can be
found at
https://google-developers.appspot.com/chart/interactive/docs/gallery/piechart#Example

-->
<html>
  <head>
    <script type="text/javascript"
src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Browser', 'Number of page views'],
          ['IE',      <?php echo preg_replace('/[^0-9]/', '', $_GET
['IE']); ?>],
          ['Chrome',  <?php echo preg_replace('/[^0-9]/', '', $_GET
['Chrome']); ?>],
          ['Firefox', <?php echo preg_replace('/[^0-9]/', '', $_GET
['FF']); ?>],
	  ['Robots', <?php echo preg_replace('/[^0-9]/', '', $_GET
['bots']); ?>],
	  ['Other Browsers', <?php echo preg_replace('/[^0-9]/', '', $_GET
['Other']); ?>]
        ]);

        var options = {
          title: 'Browser by type'
        };

        var chart = new google.visualization.PieChart
(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 260px; height: 500px;"></div>
  </body>
</html>