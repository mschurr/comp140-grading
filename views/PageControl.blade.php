{{--
	Provides a simple page controller.

	Requires the following variables:
		$page - current page number
		$pages - total number of pages
		$pageUrl = function($pagenum) { return URL; } - closure that returns url to provided page number
--}}

@if($pages > 1)

	<div class="page_control">

		Page:

		@if($page > 6)
			<a href="{{{ $pageUrl(1) }}}">1</a>

			...
		@endif

		@for($i = 5; $i > 0; $i--)
			@if($page - $i > 0)
				<a href="{{{ $pageUrl($page - $i) }}}">{{{ $page - $i }}}</a>
			@endif
		@endfor

		<a href="#" class="active">{{{ $page }}}</a>

		@for($i = 1; $i < 6; $i++)
			@if($page + $i <= $pages)
				<a href="{{{ $pageUrl($page + $i) }}}">{{{ $page + $i }}}</a>
			@endif
		@endfor

		@if($page < $pages - 5)
			...

			<a href="{{{ $pageUrl($pages) }}}">{{{ $pages }}}</a>
		@endif

	</div>

@endif