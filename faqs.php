<?php 
include('./components/header.php');
include('./config/db.php');
?>

<style>
/* FAQ Page Styles */
.faq-hero {
    width: 100%;
    height: 300px;
    background: url('./assets/images/wiifm.webp') center/cover no-repeat;
    border-radius: 8px;
    margin-bottom: 40px;
}

.faq-container {
    max-width: 1000px;
    margin: auto;
    padding: 20px;
}

.faq-heading {
    text-align: center;
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c2c2c;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.accordion {
    background-color: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.accordion-header {
    padding: 18px 20px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    position: relative;
    color: #333;
    background: #f9f9f9;
}

.accordion-header:hover {
    background: #f1f1f1;
}

.accordion-header::after {
    content: '+';
    font-size: 20px;
    position: absolute;
    right: 20px;
    top: 18px;
    transition: transform 0.3s ease;
}

.accordion.active .accordion-header::after {
    content: '-';
    transform: rotate(180deg);
}

.accordion-body {
    max-height: 0;
    overflow: hidden;
    padding: 0 20px;
    background: #fff;
    font-size: 0.95rem;
    color: #555;
    line-height: 1.6;
    transition: max-height 0.4s ease, padding 0.3s ease;
    /* justify the text alignment */
    text-align: justify;
}

.accordion.active .accordion-body {
    max-height: 1000px; /* enough for long answers */
    padding: 20px;
}
</style>

<div class="faq-hero"></div>

<div class="faq-container">
    <h2 class="faq-heading">Frequently Asked Questions</h2>

    <div class="accordion">
        <div class="accordion-header">Q1. What is the Quality of the Product?</div>
        <div class="accordion-body">
            We are using Premium Quality Perfumery Oils under the strict supervision of a Professional Perfumer, 
            who has a rich experience in designing FRESH or A REPLICA of existing perfumes to perfection.
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header">Q2. What is the final Product Quality Category in our Perfumes?</div>
        <div class="accordion-body">
            We only design <b>PARFUM</b> with the highest oil concentration <b><span style="color: #58c733ff;">(20% – 30%)</span></b> as per global quality standards 
            for long-lasting fragrance and high modulation impact.
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header">Q3. Do we have specific bottles for each quantity?</div>
        <div class="accordion-body">
            Yes, we offer all Perfumes / Attars / Essence Oils in <b>6 ML, 12 ML, 30 ML, 50 ML, and 100 ML</b>. 
            The quality remains premium across every product, although, the actual design, color, and shape of the bottle may vary, cause we use only <b>Indian-made glass bottles</b>, 
            not imported Chinese bottles.
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header">Q4. Why buy ADAAROMAS products?</div>
        <div class="accordion-body">
            Our company is owned by <b>Mr. Atul Dev Arora</b>, who also runs the <b>RUDRAKSHAA WELFARE FOUNDATION</b>, 
            a Section 8 Non-Profit Organisation under the Ministry of Corporate Affairs, Govt. of India. 
            ADAAROMAS is designed to <b>raise funds</b> for 15 <b>Philanthropic Projects</b> aligned with <b>UNSDG guidelines</b> 
            (<a href="https://www.rudraksha.org.in" target="_blank">www.rudraksha.org.in</a>).
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header">Q5. What are we contributing towards?</div>
        <div class="accordion-body">
            The funds raised support <b>Education, Environmental Protection, Healthcare, Food Distribution, Sports, 
            Animal Care, Women Empowerment, Spiritual Programs, Orphanages, Old-age Support, Acid Attack Victim Care, 
            Cremation of Unclaimed Bodies</b> and more, all approved by the Government of India.
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header">Q6. Are our revenue margins as high compared to other brands?</div>
        <div class="accordion-body">
            <b>No.</b> Our mission is to deliver <b>premium quality at the lowest possible cost</b>. 
            A majority of revenue is reinvested into <b>Philanthropic Projects</b> after clearing all dues and taxes, 
            as per the Ministry of Corporate Affairs and the Court of Law.
        </div>
    </div>
</div>

<script>
// FAQ Accordion Functionality
const accordions = document.querySelectorAll('.accordion');
accordions.forEach(acc => {
    acc.querySelector('.accordion-header').addEventListener('click', () => {
        acc.classList.toggle('active');
    });
});
</script>

<?php include('./components/footer.php'); ?>

