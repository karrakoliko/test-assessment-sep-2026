{include file="partials/header.tpl"}
<div id="category-page">
    <h2 class="category__title">{$category->getTitle()->getValue()}</h2>
    <blockquote class="category__description">{$category->getDescription()->getValue()}</blockquote>

    <h3>Articles</h3>
    {foreach $articlesLatest as $articleLatest}
        <div class="article">
            <div class="article__line">
                <div class="article__link">
                    <a href="{$urlGenerator->generate('article__show',['id' => $articleLatest->getId()])->toString()}">&#x2197;</a>
                </div>
                <div class="article__title">{$articleLatest->getTitle()->getValue()}</div>
            </div>
            <a href="{$articleLatest->getPicUrl()->toString()}" target="_blank">
                <img class="article__pic" src="{$articleLatest->getPicUrl()->toString()}" alt="article picture">
            </a>
            <article class="article__desc">{$articleLatest->getDescription()->getValue()}</article>
        </div>
    {/foreach}
</div>
{include file="partials/footer.tpl"}