<style>
    /*Main*/
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

    /*section 1*/
    .section_1 {
        width: 100%;
    }

    h1.section_1 {
        font-family: 'Inter';
        font-size: 43px;
        position: relative;
        right: 10px;
        top: 246px;
    }

    img.section_1 {
        width: 500px;
        float: left;
        position: relative;
        left: 10px;
        top: 222px;
        z-index: 1;
    }

    button.section_1 {
        background-color: #c62e2e;
        border: none;
        border-radius: 30px;
        position: relative;
        bottom: 1px;
        color: white;
        font-size: 20px;
        cursor: pointer;
        font-family: 'Inter';
        padding: 8px 16px;
        transition: all 0.3s ease;
        width: 203px;
        height: 50px;
        top: 235px;
    }

    button.section_1:hover {
        background-color: #F95454;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
    }

    /*Background*/
    .circle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #0D92F4, #77CDFF);
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
        top: 80%;
        left: 50px;
        animation-delay: 2s;
    }

    .circle2 {
        width: 100px;
        height: 100px;
        top: 70%;
        left: 80%;
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
        left: 80%;
    }

    /*section 2*/
    .section.section2 {
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: left;
    }

    .st2-title {
        text-align: center;
        font-size: 28px;
        color: #d9534f;
        margin-bottom: 0px;
        margin-top: 140px;
    }

    .health-article {
        background-color: #fff;
        border-left: 5px solid #d9534f;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 8px;
        transition: transform 0.3s;
    }

    .health-article:hover {
        transform: translateY(-4px);
    }

    .health-article a {
        text-decoration: none;
    }

    .article-title {
        font-size: 20px;
        color: #333;
        margin-bottom: 10px;
        text-decoration: none;
    }

    .article-title:hover {
        text-decoration: underline;
        color: #c9302c;
    }

    .article-description {
        font-size: 16px;
        color: #555;
        margin-bottom: 10px;
    }

    .article-meta {
        font-size: 14px;
        color: #888;
    }

    /*section 3*/
    .ai-assistant-section {
        background: #f0f9ff;
        padding: 60px 20px;
        text-align: center;
    }

    .section3 {
        position: relative;
        width: 100%;
        height: 100%;
        background: linear-gradient(270deg, #F95454, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
        background-size: 1000% 1000%;
        animation: waveGradient 20s ease infinite;
    }

    @keyframes waveGradient {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    h2.st3 {
        position: relative;
        top: 121px;
        color: white;
    }

    p.st3 {
        position: relative;
        top: 113px;
        color: white;
    }

    .chatbox {
        width: 100%;
        max-width: 600px;
        margin: 30px auto 0;
        border: 1px solid #ccc;
        border-radius: 12px;
        background: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        top: 100px;
    }

    .chat-messages {
        height: 280px;
        overflow-y: auto;
        padding: 15px;
        text-align: left;
    }

    .chat-messages p {
        margin: 10px 0;
    }

    .chat-messages .bot {
        color: #1e90ff;
    }

    .chat-messages .user {
        text-align: right;
        color: #333;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #eee;
    }

    .chat-input input {
        flex: 1;
        padding: 10px;
        border: none;
        outline: none;
    }

    .chat-input button {
        padding: 10px 20px;
        border: none;
        background: #1e90ff;
        color: white;
        cursor: pointer;
    }

    /*Section 4*/
    .section4 {
        background: linear-gradient(270deg,
                rgba(119, 205, 255, 1) 0%,
                rgba(174, 225, 254, 1) 33%,
                rgba(191, 231, 254, 1) 63%,
                rgba(226, 243, 253, 1) 100%);
        background-size: cover;
    }

    .site-footer {
        color: #333;
        padding: 40px 20px 20px;
        font-family: 'Inter';
        position: relative;
        top: 100px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .site-footer.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .footer-container {
        background-color: rgba(255, 255, 255, 0.75);
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
    }


    .footer-section h3 {
        font-size: 22px;
        font-weight: 600;
        color: #F95454;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        letter-spacing: 0.5px;
    }

    .footer-section p,
    .footer-section ul {
        font-size: 15px;
        line-height: 1.6;
        color: #444;
        text-shadow: 0.5px 0.5px 1px rgba(0, 0, 0, 0.08);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        font-size: 13px;
        border-top: 1px solid #000;
        margin-top: 15px;
        text-shadow: 0.5px 0.5px 1px rgba(0, 0, 0, 0.1);
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li a {
        text-decoration: none;
        transition: color 0.3s ease;
        color: #333;
    }

    .footer-section ul li a:hover {
        color: #1e90ff;
        text-shadow: 0 0 3px rgba(30, 144, 255, 0.3);
    }


    .footer-section img {
        margin-right: 10px;
        transition: transform 0.3s;
    }

    .footer-section img:hover {
        transform: scale(1.1);
    }
</style>
<div id="fullpage">
    <div class="section  section1">
        <img class="section_1" src="assets/images/consultant.png" alt="">
        <h1 class="section_1">Do you want a health advice right now?</h1>
        <button onclick="location.href='index.php?route=recommendation'" class="section_1">Recommend now</button>
        <div class="circle circle1"></div>
        <div class="circle circle2"></div>
        <div class="circle circle3"></div>
        <div class="circle circle4"></div>
    </div>
    <div class="section section2">
        <div class="container st2">
            <h2 class="st2-title"> Health Knownledge</h2>
            <div class="health-article">
                <a href="https://vnexpress.net/cach-cham-soc-nguoi-cao-tuoi-tai-nha-123456.html" target="_blank" rel="noopener noreferrer">
                    <h3 class="article-title">Tác động của giấc ngủ đến sức khỏe tinh thần</h3>
                </a>
                <p class="article-description">
                    Nghiên cứu mới cho thấy rằng ngủ đủ 7–9 giờ mỗi đêm giúp cải thiện trí nhớ, giảm căng thẳng và ngăn ngừa trầm cảm.
                </p>
                <p class="article-meta">
                    ✍️ <strong>Tác giả:</strong> Minh An &nbsp; | &nbsp; 📅 <strong>Ngày:</strong> 08/06/2025 &nbsp; | &nbsp; 🏷️ <strong>Thể loại:</strong> Sức khỏe tinh thần
                </p>
            </div>

            <div class="health-article">
                <a href="https://tuoitre.vn/dinh-duong-cho-benh-tieu-duong-654321.html" target="_blank" rel="noopener noreferrer">
                    <h3 class="article-title">Bí quyết ăn uống giúp phòng ngừa tiểu đường type 2</h3>
                </a>
                <p class="article-description">
                    Chuyên gia khuyến nghị ăn nhiều rau xanh, ngũ cốc nguyên hạt và hạn chế đường tinh luyện để kiểm soát lượng đường huyết.
                </p>
                <p class="article-meta">
                    ✍️ <strong>Tác giả:</strong> Lan Hương &nbsp; | &nbsp; 📅 <strong>Ngày:</strong> 07/06/2025 &nbsp; | &nbsp; 🏷️ <strong>Thể loại:</strong> Dinh dưỡng
                </p>
            </div>
        </div>
    </div>
    <div class="section section3">
        <h2 class="st3">My health assistant</h2>
        <p class="st3">ask me anything about health, medicine,...</p>

        <div class="chatbox">
            <div class="chat-messages" id="chat-messages"></div>
            <form class="chat-input" onsubmit="sendMessage(event)">
                <input type="text" id="user-input" placeholder="Type in your question..." required />
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    <div class="section section4">
        <footer class="site-footer">
            <div class="footer-container">
                <div class="footer-section about">
                    <h3>About us</h3>
                    <p>This website provides reliable health information, advice and suggestions to the community. Not a substitute for professional medical diagnosis.</p>
                </div>

                <div class="footer-section contact">
                    <h3>Contact</h3>
                    <p>Email: HealMate.info@gmail.com</p>
                    <p>Hotline: 1900 123 456</p>
                </div>

                <div class="footer-section social">
                    <h3>Connect with us</h3>
                    <a href="#"><img src="https://img.icons8.com/ios-filled/24/facebook-new.png" alt="Facebook" /></a>
                    <a href="#"><img src="https://img.icons8.com/ios-filled/24/instagram-new.png" alt="Instagram" /></a>
                    <a href="#"><img src="https://img.icons8.com/ios-filled/24/youtube-play.png" alt="YouTube" /></a>
                </div>
                <div class="footer-bottom">
                    <p>© 2025 HealMate. All rights reserved.</p>
                </div>
        </footer>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js"></script>
<script>
    /*Main*/
    new fullpage('#fullpage', {
        autoScrolling: true,
        navigation: true,
        scrollBar: false,
    });

    /*chatbox*/
    function sendMessage(event) {
        event.preventDefault();
        const input = document.getElementById("user-input");
        const message = input.value.trim();
        if (!message) return;

        appendMessage("Bạn", message, "user");
        input.value = "";

        // Bot response (giản lược)
        setTimeout(() => {
            const reply = getBotResponse(message);
            appendMessage("Trợ lý", reply, "bot");
        }, 500);
    }

    function appendMessage(sender, message, type) {
        const chatMessages = document.getElementById("chat-messages");
        const msg = document.createElement("p");
        msg.classList.add(type);
        msg.innerHTML = `<strong>${sender}:</strong> ${message}`;
        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function getBotResponse(userInput) {
        const text = userInput.toLowerCase();
        if (text.includes("uống thuốc")) return "Tôi sẽ nhắc bạn uống thuốc đúng giờ!";
        if (text.includes("huyết áp")) return "Hãy đo huyết áp thường xuyên và ghi lại chỉ số.";
        return "Xin lỗi, tôi chưa hiểu rõ. Bạn có thể hỏi về sức khoẻ, thuốc, hoặc chế độ dinh dưỡng.";
    }

    //fade-in
    document.addEventListener("DOMContentLoaded", function () {
        const footer = document.querySelector(".site-footer");

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    footer.classList.add("visible");
                    observer.unobserve(entry.target); // Chỉ thực hiện 1 lần
                }
            });
        }, {
            threshold: 0.2
        });

        observer.observe(footer);
    });
</script>