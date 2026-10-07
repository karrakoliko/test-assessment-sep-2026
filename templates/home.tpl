<h1>Blog</h1>

{foreach $homePageCategories as $homePageCategory}
    <div class="category__title">{$homePageCategory->getCategory()->getTitle()->getValue()}</div>
    <div class="category__btn-all-articles">
        <a href="{$homePageCategory->getLinkToAllArticles()->toString()}">All articles</a>
    </div>
    <ul>
        {foreach $homePageCategory->getArticlesLatest() as $homePageArticle}
            <li>{$homePageArticle->getTitle()->getValue()}</li>
        {/foreach}

    </ul>
{/foreach}