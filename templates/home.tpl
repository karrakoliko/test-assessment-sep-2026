{include file="partials/header.tpl"}
<div id="home-page">
    {foreach $homePageCategories as $homePageCategory}
        <div class="category">
            <div class="category__line">
                <a class="category__btn-all-articles"
                   href="{$urlGenerator->generate('category__show',['id' => $homePageCategory->getCategory()->getId()])->toString()}">&#x2197;</a>
                <div class="category__title">{$homePageCategory->getCategory()->getTitle()->getValue()}</div>
            </div>
            <ul>
                {foreach $homePageCategory->getArticlesLatest() as $homePageArticle}
                    <div class="article">
                        <a class="article__link"
                           href="{$urlGenerator->generate('article__show',['id' => $homePageArticle->getId()])->toString()}">&#x2197;</a>
                        <div class="article__title">{$homePageArticle->getTitle()->getValue()}</div>
                    </div>
                {/foreach}

            </ul>
        </div>
    {/foreach}
</div>
{include file="partials/footer.tpl"}