<!DOCTYPE html>
<!-- saved from url=(0053)https://getbootstrap.com/docs/4.0/examples/jumbotron/ -->
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Khien</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/jumbotron/">

    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../assets/dist/css//jumbotron.css" rel="stylesheet">
</head>

<body data-new-gr-c-s-check-loaded="14.1126.0" data-gr-ext-installed="">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/#">Home <span
                        class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="/#">Disabled</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Tools</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="/bang-diem-xls">Chuyển đổ bảng điểm xls</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/bang-diem/search">Bảng điểm </a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

<main role="main">

    <div style="padding-top: 3.5rem;">
        <div class="container">
            <!-- Example row of columns -->
            <div class="row">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron mt-5">

        <div class="container">
            <h1 class="display-3">Hello, world!</h1>
            <p>Have a nice day!</p>
        </div>
    </div>
</main>

<footer class="container">
    <p>© 2023</p>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="../assets/dist/js/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../assets/dist/js/jquery-3.2.1.slim.min.js"><\/script>')</script>
<script src="../assets/dist/js/popper.min.js"></script>
<script src="../assets/dist/js/bootstrap.min.js"></script>


</body>
<grammarly-desktop-integration data-grammarly-shadow-root="true">
    <template shadowrootmode="open">
        <style>
            div.grammarly-desktop-integration {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            div.grammarly-desktop-integration:before {
                content: attr(data-content);
            }
        </style>
        <div aria-label="grammarly-integration" role="group" tabindex="-1" class="grammarly-desktop-integration"
             data-content="{&quot;mode&quot;:&quot;full&quot;,&quot;isActive&quot;:true,&quot;isUserDisabled&quot;:false}"></div>
    </template>
</grammarly-desktop-integration>
</html>
