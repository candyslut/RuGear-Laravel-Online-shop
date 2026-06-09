{{-- A single sticker-pack preview thumbnail, branching on file type.
     Calm by default: it shows a static first frame and animates only on hover
     (so the whole shelf isn't moving at once).

     Pass $autoplay = true for small, always-on thumbnails (e.g. the picker's
     pack-tab mini icons) where a reliably-painted frame matters more than calm:
     Lottie then mounts via the Alpine x-lottie directive and loops, instead of
     the hover-paused poster (which can stay blank for some .json files).

     Inputs: $src (Storage URL), $size (CSS length, e.g. '5rem'), $autoplay (bool). --}}
@php($sz = $size ?? '5rem')
@php($auto = $autoplay ?? false)
@if(\Illuminate\Support\Str::endsWith($src, '.json'))
    @if($auto)
        {{-- x-lottie.loop: Alpine mounts + autoplays it, guaranteeing a painted frame. --}}
        <canvas x-lottie.loop="'{{ $src }}'" width="200" height="200"
                style="width:{{ $sz }};height:{{ $sz }};"></canvas>
    @else
        {{-- data-lottie-hover: paused first frame, plays on pointer enter --}}
        <canvas data-lottie-src="{{ $src }}" data-lottie-hover width="200" height="200"
                style="width:{{ $sz }};height:{{ $sz }};"></canvas>
    @endif
@elseif(\Illuminate\Support\Str::endsWith($src, '.webm'))
    {{-- #t=0.001 makes the browser paint the first frame as a still poster.
         On leave we rewind to that first frame, otherwise the thumbnail freezes
         on a random mid-animation frame. --}}
    <video src="{{ $src }}#t=0.001" muted playsinline loop preload="metadata" @if($auto) autoplay @endif
           onmouseenter="this.play()" onmouseleave="this.pause(); try { this.currentTime = 0.001; } catch (e) {}"
           style="width:{{ $sz }};height:{{ $sz }};object-fit:contain;"></video>
@else
    <img src="{{ $src }}" style="width:{{ $sz }};height:{{ $sz }};object-fit:contain;" loading="lazy" alt="">
@endif
