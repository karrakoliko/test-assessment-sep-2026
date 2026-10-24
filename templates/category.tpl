{include file="partials/header.tpl"}
<div id="category-page">
    <h2 class="category__title">{$category->getTitle()->getValue()}</h2>
    <blockquote class="category__description">{$category->getDescription()->getValue()}</blockquote>

    <h3>Articles</h3>
    {foreach $articlesLatest as $articleLatest}
        <div class="article">
            <div class="article__line">
                <div class="article__link">
                    <a href="{$uriGenerator->generate('article__show',['id' => $articleLatest->getId()])->toString()}">&#x2197;</a>
                </div>
                <div class="article__title">{$articleLatest->getTitle()->getValue()}</div>
            </div>
            <a href="{$articleLatest->getPicUri()->toString()}" target="_blank">
                <img class="article__pic" src="{$articleLatest->getPicUri()->toString()}" alt="article picture">
            </a>
            <article class="article__desc">{$articleLatest->getDescription()->getValue()}</article>
        </div>
    {foreachelse}
        No items
    {/foreach}

    {if $page > 1 || count($articlesLatest) === $paginationPerPage}
    <div class="pagination">
        {if $page > 1}
            <a class="pagination__prev" href="{$uriGenerator->generate('category__show',['id'=>$category->getId()],['page'=>$page - 1])->toString()}">Prev</a>
        {/if}
        {if count($articlesLatest) === $paginationPerPage}
            <a class="pagination__next" href="{$uriGenerator->generate('category__show',['id'=>$category->getId()],['page'=>$page + 1])->toString()}">Next</a>
        {/if}
    </div>
    {/if}

</div>
{include file="partials/footer.tpl"}