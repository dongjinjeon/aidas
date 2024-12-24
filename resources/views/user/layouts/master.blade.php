<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($page_title) ? __($page_title) : __("Dashboard")) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900&family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    @include('partials.header-asset')
    
    @stack("css")
</head>
<body class="{{ selectedLangDir() ?? "ltr"}}">


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Preloader
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div id="preloader"></div>
@include('frontend.partials.preloader')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Preloader
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<div id="body-overlay" class="body-overlay"></div>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    @include('user.partials.side-nav')

    <div class="main-wrapper">
        <div class="main-body-wrapper">
            @include('user.partials.top-nav')
            <div class="body-wrapper">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


@include('partials.footer-asset')
@include('user.partials.push-notification')
<script src="{{ asset('public/frontend/js/apexcharts.js') }}"></script> 
@stack("script") 
<script>  
  var chart1 = $('#chart1');
  var chart_one_data = chart1.data('chart_one_data');
  var month_day = chart1.data('month_day');
  var options = {
        series: [{
            name: 'Add Money',
            color: "#2167e8",
            data: chart_one_data.add_money
        }, {
            name: 'Withdraw Money',
            color: "#44a08d",
            data: chart_one_data.withdraq_money
        }, {
            name: 'Send Money',
            color: "#12b883",
            data: chart_one_data.send_money
        }],
        chart: {
        type: 'bar',
        toolbar: {
            show: false
        },
        height: 350
        },
        plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 5,
            endingShape: 'rounded'
        },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            type: 'datetime',
            categories: month_day,
        },
        yaxis: {
        title: {
            text: '$ (thousands)'
        }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
        y: {
            formatter: function (val) {
            return val
            }
        }
        }
        };

    var chart = new ApexCharts(document.querySelector("#chart1"), options);
    chart.render();
</script>
<script>
  var swiper = new Swiper(".mySwiper", {
      slidesPerView: 4,
      spaceBetween: 30,
      freeMode: true,
      autoplay: {
          delay: 6000,
          disableOnInteraction: false
      },
      breakpoints: {
          '480': {
              slidesPerView: 1,
              spaceBetween: 30,
          },
          '768': {
              slidesPerView: 2,
              spaceBetween: 20,
          },
          '820': {
              slidesPerView: 2,
              spaceBetween: 20,
          },
          '912': {
              slidesPerView: 2,
              spaceBetween: 20,
          },
      },
  });
</script>

</body>
</html>