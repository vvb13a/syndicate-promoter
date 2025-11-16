@if($hreflang = $getHreflang())
    <!-- Hreflang -->
    {{$hreflang->render()}}
@endif

@if($title = $getTitle())
    <!-- Meta Data -->
    <title>{{ $title }}</title>
@endif
@if($description = $getDescription())
    <meta name="description" content="{{ $description }}">
@endif
@if($keywords = $getKeywords())
    <meta name="keywords" content="{{ $keywords }}">
@endif
@if($robots = $getRobots())
    <meta name="robots" content="{{ $robots }}">
@endif
@if($canonicalUrl = $getCanonicalUrl())
    <link rel="canonical" href="{{ $canonicalUrl }}">
@endif

@if($openGraph = $getOpenGraph())
    <!-- OpenGraph -->
    {!! $openGraph !!}
@endif

@if($twitter = $getTwitter())
    <!-- Twitter -->
    {!! $twitter !!}
@endif

@if($schema = $getSchema())
    <!-- Schema Org -->
    {!! $schema->toScript() !!}
@endif
