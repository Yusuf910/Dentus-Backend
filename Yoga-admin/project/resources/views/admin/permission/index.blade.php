
                           	@if (!empty($data))
                           	<?php $i=1; ?>
                           		@foreach ($data as $a)
                           		<br>{{$a->slug}}
                           <?php $i++; ?>
								@endforeach
                           	@endif
                           