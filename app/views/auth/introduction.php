<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Introduction</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    .section {
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .fp-overflow {
        width: 100%;
        height: 100%;
    }

    .fp-watermark {
        display: none !important;
    }

    .section_1 {
        width: 100%;
    }

    main {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
    }

    img.logo_st1 {
        width: 100px;
        position: relative;
        top: 150px;
    }

    h1.logo_st1 {
        margin-bottom: 10px;
        color: #F95454;
        position: relative;
        top: 128px;
        left: 553px;
        width: 178px;
    }

    h1 {
        margin-bottom: 10px;
        color: #2e86de;
    }

    p {
        margin-bottom: 35px;
        position: relative;
        font-size: 17px;
        width: 1000px;
        top: 136px;
        left: 140px;
    }

    .divider {
        border: none;
        height: 2px;
        background-color: #ccc;
        margin: 20px 0;
        position: relative;
        top: 125px;
    }

    /*Background*/
    .circle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #F95454, #C62E2E);
        animation: float 6s ease-in-out infinite, glow 4s ease-in-out infinite alternate;
        opacity: 0.9;
        filter: brightness(1);
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes glow {
        0% {
            filter: brightness(0.7);
        }

        100% {
            filter: brightness(1.3);
        }
    }

    /* Mỗi vòng tròn có kích thước và vị trí khác nhau */
    .circle1 {
        width: 150px;
        height: 150px;
        top: 90%;
        left: -7px;
        animation-delay: 2s;
    }

    .circle2 {
        width: 100px;
        height: 100px;
        top: 70%;
        left: 90%;
        animation-delay: 4s;
    }

    .circle3 {
        width: 200px;
        height: 200px;
        top: 5%;
        left: 60%;
    }

    .circle4 {
        width: 200px;
        height: 200px;
        top: 80%;
        left: 90%;
    }

    /*Section2*/
    h3 {
        position: relative;
        top: 138px;
        font-size: 27px;
        background: linear-gradient(90deg, #0D92F4, #F95454);
        /* màu gradient */
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .multi-column-list {
        list-style-type: none;
        column-count: 4;
        column-gap: 32px;
        padding-left: 23px;
        position: relative;
        top: 405px;
    }

    /*Background*/
    .circle_st2 {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #0D92F4, #F95454);
        animation: glow 4s ease-in-out infinite alternate;
        opacity: 0.9;
        filter: brightness(1);
    }

    @keyframes glow {
        0% {
            filter: brightness(0.8);
        }

        100% {
            filter: brightness(1.4);
        }
    }

    .circle-container {
        position: relative;
        width: 150px;
        height: 150px;
        float: left;
    }

    .circle_st2.circle1 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 67px;
    }

    .cx {
        position: absolute;
        top: 133%;
        left: 118%;
        width: 191px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle2 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 155%;
    }

    .tt {
        position: absolute;
        top: 134%;
        left: 229%;
        ;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle3 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 266%;
    }

    .cnh {
        position: absolute;
        top: 133%;
        left: 340%;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle4 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 380%;
    }

    .\32 47 {
        position: absolute;
        top: 133%;
        left: 454%;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }
</style>

<body>
    <?php
    include('header.php')
    ?>
    <!-- Nội dung chính -->
    <div id="fullpage">
        <div class="section  section1">
            <div class="logo_st1">
                <img class="logo_st1" src="assets/images/logo.png" alt="">
                <h1 class="logo_st1">HealMate</h1>
            </div>
            <p>
                HealMate is an online platform that provides you with quick access to reliable information, advice and guidance.
We provide healthcare solutions and guidance tailored to your individual needs.
            </p>
            <hr class="divider" />
            <p>
                With the support of modern technology, our website is committed to providing an easy, safe and effective medical information search experience.
            </p>
            <hr class="divider" />
            <p>
                Whether you're learning about symptoms, nutrition, or disease prevention, Health Tips is here to help you on your journey to wellness.
            </p>
            <div class="circle circle1"></div>
            <div class="circle circle2"></div>
            <div class="circle circle3"></div>
            <div class="circle circle4"></div>
        </div>
        <div class="section">
            <h3>Our strengths</h3>
            <ul class="multi-column-list">
                <li>Updated, accurate information from reputable sources.</li>
                <li>Friendly interface, easy to use on all devices.</li>
                <li>Personalized advice based on data and medical expertise.</li>
                <li>24/7 Support.</li>
            </ul>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle1"></div>
                <img class="cx" src="assets/images/chinh_xac.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle2"></div>
                <img class="tt" src="assets/images/than_thien.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle3"></div>
                <img class="cnh" src="assets/images/ca_nhan_hoa.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle4"></div>
                <img class="247" src="assets/images/247.png" alt="" />
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js"></script>

    <script>
        new fullpage('#fullpage', {
            autoScrolling: true,
            navigation: true,
            scrollBar: false,
        });
    </script>
</body>

</html>