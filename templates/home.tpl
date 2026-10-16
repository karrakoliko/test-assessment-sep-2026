<h1>Blog</h1>

{foreach $homePageCategories as $homePageCategory}
    <div class="category_home-page">
        <a class="category__btn-all-articles" href="{$urlGenerator->generate('category__show',['id' => $homePageCategory->getCategory()->getId()])->toString()}">&#x2197;</a>
        <div class="category__title">{$homePageCategory->getCategory()->getTitle()->getValue()}</div>
        <ul class="category__articles">
            {foreach $homePageCategory->getArticlesLatest() as $homePageArticle}
                <li class="category__article">{$homePageArticle->getTitle()->getValue()}</li>
            {/foreach}

        </ul>
    </div>
{/foreach}