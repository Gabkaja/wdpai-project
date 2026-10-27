<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>All cards</h1>

        <section aria-labelledby="cards-heading">
            <h2 id="cards-heading">Collection overview</h2>
            <?php $this->partial('card-list', ['cards' => $cards]); ?>
        </section>
    </main>

<?php $this->partial('footer'); ?>