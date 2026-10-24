{include file="partials/header.tpl"}
<div id="article-page">
    <div class="article">
        <h2 class="article__title">{$article->getTitle()->getValue()}</h2>
        <a href="{$article->getPicUri()->toString()}" target="_blank">
            <img class="article__pic" src="{$article->getPicUri()->toString()}" alt="article picture">
        </a>
        <blockquote class="article__description">{$article->getDescription()->getValue()}</blockquote>
        <article class="article__content">{$article->getContent()->getValue()}</article>
    </div>
</div>
{include file="partials/footer.tpl"}