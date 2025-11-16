{{--@formatter:off--}}
@php
$additionalData = $getAdditionalData();
$keyword = $getKeyword();
$title = $getTitle();
$content = $getContent();
@endphp
Please generate the SEO metadata as instructed for the following information:

Content Headline:
{{ $title }}

@if($keyword)
Target Keyword (provided by user):
{{ $keyword }}
@endif

@if(!empty($additionalData))
Additional Context (key-value):
@foreach($additionalData as $k => $v)
@php($keyAllowed = is_string($k) || is_int($k))
@php($valAllowed = is_string($v) || is_int($v))
@if($keyAllowed && $valAllowed)
- {{ $k }}: {{ $v }}
@endif
@endforeach
@endif

Content Body:
{!! $content !!}
