<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main class="error-page">
        <section class="error-page__panel" aria-labelledby="error-title">
            <span class="error-page__icon icon" aria-hidden="true">signal_disconnected</span>
            <p class="error-page__code" aria-hidden="true">404</p>

            <h1 class="error-page__title" id="error-title">This page left the stream</h1>
            <p class="error-page__text">
                The page or card you are looking for is not in the catalog.
                It may have been removed, renamed or never existed.
            </p>

            <div class="error-page__actions">
                <a class="button button--primary" href="/">Back to the catalog</a>
                <a class="button button--secondary" href="/cards">Browse all cards</a>
            </div>

            <p class="error-page__hint">Error code: <strong>404 &ndash; Not Found</strong></p>
        </section>
    </main>

<?php $this->partial('footer'); ?>
