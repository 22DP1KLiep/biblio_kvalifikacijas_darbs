<script>

</script>


<template>
    <footer>
        <div class="footer-container">
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p>Email: info@example.com</p>
                <p>Phone: +123 456 7890</p>
            </div>
            <div class="footer-social">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Biblio. All rights reserved.</p>
        </div>
    </footer>
</template>


<style>
footer {
    background-color: #213555; /* Background color for the footer */
    color: rgb(255, 235, 235); /* Text color for the footer */
    padding: 20px 0; /* Padding for the footer */
}

.footer-container {
    display: flex; /* Use flexbox for layout */
    justify-content: space-around; /* Space out the sections */
    max-width: 1200px; /* Max width for the footer */
    margin: 0 auto; /* Center the footer */
}

.footer-links, .footer-contact, .footer-social {
    flex: 1; /* Allow sections to grow equally */
    padding: 0 20px; /* Padding for sections */
}

.footer-links h3, .footer-contact h3, .footer-social h3 {
    margin-bottom: 10px; /* Space below headings */
}

.footer-links ul, .footer-social ul {
    list-style-type: none; /* Remove bullet points */
    padding: 0; /* Remove default padding */
}

.footer-links li, .footer-social li {
    margin: 5px 0; /* Space between links */
}

.footer-links a, .footer-social a {
    color: rgb(255, 235, 235); /* Link color */
    text-decoration: none; /* Remove underline */
}

.footer-links a:hover, .footer-social a:hover {
    text-decoration: underline; /* Underline on hover */
}

.footer-bottom {
    text-align: center; /* Center the copyright text */
    margin-top: 20px; /* Space above the copyright text */
}

</style>
