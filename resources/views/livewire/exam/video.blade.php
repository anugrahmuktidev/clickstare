{{-- resources/views/livewire/exam/video.blade.php --}}
<div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    <h1 class="text-xl font-bold">Tonton Video</h1>

    @if (session('success'))
        <div class="p-3 rounded bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($video)
        {{-- <div>
            <h2 class="font-semibold text-lg">{{ $video->judul }}</h2>
            @if($video->deskripsi)
                <p class="text-gray-600">{{ $video->deskripsi }}</p>
            @endif>
        </div> --}}

        <video
            controls
            class="w-full rounded"
            preload="metadata"
            playsinline
            controlsList="nodownload noplaybackrate noremoteplayback"
            disablePictureInPicture
            oncontextmenu="return false;"
            onended="window.Livewire?.find('{{ $this->getId() }}')?.call('markFinished')"
            id="exam-session-video"
        >
            <source src="{{ $video->video_url }}" type="{{ $video->mime ?? 'video/mp4' }}">
            Browser Anda tidak mendukung pemutar video HTML5.
        </video>

        <div class="space-y-2">
            @error('video')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button
                wire:click="completeVideo"
                @disabled(! $canComplete)
                class="px-4 py-2 rounded text-white
                       {{ $canComplete ? 'bg-emerald-600 hover:bg-emerald-700'
                                        : 'bg-gray-300 cursor-not-allowed' }}"
            >
                Selesai Menonton
            </button>
        </div>
    @else
        <p>Belum ada video edukasi.</p>
    @endif
</div>

<script>
    (() => {
        const patchPlaybackRateProperty = () => {
            const proto = HTMLMediaElement.prototype;

            if (proto.__clicstareRatePatched) {
                return;
            }

            const originalPlayback = Object.getOwnPropertyDescriptor(proto, 'playbackRate');
            const originalDefault = Object.getOwnPropertyDescriptor(proto, 'defaultPlaybackRate');

            if (!originalPlayback || !originalPlayback.set || !originalPlayback.get) {
                return;
            }

            const clamp = (value) => (Number.isFinite(value) ? 1 : 1);

            Object.defineProperty(proto, 'playbackRate', {
                configurable: true,
                enumerable: originalPlayback.enumerable,
                get() {
                    return 1;
                },
                set(value) {
                    try {
                        originalPlayback.set.call(this, clamp(value));
                    } catch (error) {
                        originalPlayback.set.call(this, 1);
                    }
                },
            });

            if (originalDefault?.set && originalDefault?.get) {
                Object.defineProperty(proto, 'defaultPlaybackRate', {
                    configurable: true,
                    enumerable: originalDefault.enumerable,
                    get() {
                        return 1;
                    },
                    set(value) {
                        try {
                            originalDefault.set.call(this, clamp(value));
                        } catch (error) {
                            originalDefault.set.call(this, 1);
                        }
                    },
                });
            }

            proto.__clicstareRatePatched = true;
        };

        const initGuard = () => {
            patchPlaybackRateProperty();

            const video = document.getElementById('exam-session-video');
            if (!video || video.dataset.guard === 'true') {
                return;
            }

            if (!document.getElementById('exam-video-style')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'exam-video-style';
                styleEl.textContent = `
#exam-session-video::-webkit-media-controls-timeline {
    opacity: 0 !important;
    pointer-events: none !important;
    height: 0 !important;
}

#exam-session-video::-webkit-media-controls-current-time-display,
#exam-session-video::-webkit-media-controls-time-remaining-display,
#exam-session-video::-webkit-media-controls-seek-back-button,
#exam-session-video::-webkit-media-controls-seek-forward-button {
    display: none !important;
}

#exam-session-video::-moz-range-track,
#exam-session-video::-moz-range-progress,
#exam-session-video::-moz-range-thumb {
    visibility: hidden !important;
    pointer-events: none !important;
}

#exam-session-video::-ms-track {
    visibility: hidden;
    color: transparent;
}

#exam-session-video::-ms-fill-lower,
#exam-session-video::-ms-fill-upper,
#exam-session-video::-ms-thumb {
    display: none;
}`;
                document.head.appendChild(styleEl);
            }

            video.dataset.guard = 'true';

            const tolerance = 0.25;
            let lastAllowedTime = 0;
            let locking = false;

            const enforcePlaybackRate = () => {
                if (video.defaultPlaybackRate !== 1) {
                    video.defaultPlaybackRate = 1;
                }

                if (video.playbackRate !== 1) {
                    const wasPaused = video.paused;
                    try {
                        video.playbackRate = 1;
                    } catch (error) {
                        // ignore
                    }

                    if (wasPaused !== video.paused) {
                        wasPaused ? video.pause() : video.play();
                    }
                }
            };

            const clampForward = () => {
                if (locking) return;
                if (video.currentTime > lastAllowedTime + tolerance) {
                    locking = true;
                    video.currentTime = lastAllowedTime;
                }
            };

            const preventSkipKeys = (event) => {
                if ([
                    'ArrowLeft',
                    'ArrowRight',
                    'Home',
                    'End',
                    'PageUp',
                    'PageDown',
                    'MediaTrackNext',
                    'MediaTrackPrevious',
                    '>',
                    '<',
                    ',',
                    '.',
                ].includes(event.key)) {
                    event.preventDefault();
                    clampForward();
                    enforcePlaybackRate();
                }
            };

            video.addEventListener('loadedmetadata', () => {
                lastAllowedTime = 0;
                video.currentTime = 0;
                enforcePlaybackRate();
            });

            video.addEventListener('timeupdate', () => {
                enforcePlaybackRate();

                if (!locking) {
                    lastAllowedTime = Math.max(lastAllowedTime, video.currentTime);
                }
            });

            video.addEventListener('seeking', clampForward);

            video.addEventListener('seeked', () => {
                locking = false;
            });

            video.addEventListener('play', enforcePlaybackRate);

            video.addEventListener('ratechange', enforcePlaybackRate);

            video.addEventListener('pointerup', clampForward);

            video.addEventListener('wheel', (event) => {
                if (event.ctrlKey || event.shiftKey) {
                    event.preventDefault();
                    enforcePlaybackRate();
                }
            }, { passive: false });

            document.addEventListener('keydown', preventSkipKeys, true);

            const intervalId = setInterval(enforcePlaybackRate, 500);

            video.addEventListener('ended', () => {
                lastAllowedTime = video.duration;
                enforcePlaybackRate();
                clearInterval(intervalId);
            }, { once: true });
        };

        document.addEventListener('DOMContentLoaded', initGuard);
        document.addEventListener('livewire:load', initGuard);
        document.addEventListener('livewire:navigated', initGuard);

        if (window.Livewire) {
            window.Livewire.hook('message.processed', initGuard);
        }
    })();
</script>
