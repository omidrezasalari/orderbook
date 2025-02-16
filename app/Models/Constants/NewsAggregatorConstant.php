<?php

namespace App\Models\Constants;

class NewsAggregatorConstant
{

    public const BBC_SOURCE_ID = 1;
    public const BBC_SOURCE_URL = 'https://newsapi.org/v2/top-headlines?sources=bbc-news';
    public const BBC_SOURCE_API_KEY = 'YOUR_BBC_KEY';
    public const GUARDIAN_SOURCE_ID = 2;
    public const GUARDIAN_SOURCE_URL = 'https://content.guardianapis.com/search';
    public const GUARDIAN_SOURCE_API_KEY = 'YOUR_GUARDIAN_API_KEY';
    public const NEWS_API_SOURCE_ID = 3;
    public const NEWS_API_SOURCE_URL = 'https://newsapi.org/v2/top-headlines';
    public const NEWS_API_SOURCE_API_KEY = 'YOUR_NEWSAPI_KEY';
}
