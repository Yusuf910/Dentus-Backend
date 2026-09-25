@if ($paginator->hasPages())
<?php //dd($paginator->appends(request()->input())->links()); ?>
    <ul class="pagination mb-0">
       
        @if ($paginator->onFirstPage())
            <!-- <li class="page-item disabled"><a class="page-link">← Previous</a></li> -->
        @else
            <li class="page-item"><a class="page-link" href="{{$paginator->appends(request()->input())->previousPageUrl()}}" rel="prev">← Previous</a></li>
        @endif
        @foreach ($elements as $element)
           
            @if (is_string($element))
                <li class="page-item disabled"><a class="page-link">{{ $element }}</a></li>
            @endif


           
            @if (is_array($element))
                @foreach ($element as $page => $url)
                
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active my-active"><a class="page-link">{{ $page }}</a></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{$url}}&name={{request()->input('name')}}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

      
        @if ($paginator->hasMorePages())
            <li class="page-item"><a href="{{$paginator->appends(request()->input())->nextPageUrl()}}" class="page-link" rel="next">Next →</a></li>
        @else
            <!-- <li class="page-item disabled"><a class="page-link">Next →</a></li> -->
        @endif
    </ul>
@endif 