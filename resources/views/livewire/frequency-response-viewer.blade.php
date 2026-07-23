<div>
    @if (! $hasData)
        <div class="border border-dashed border-base-300 rounded-box p-8 text-center text-base-content/50">
            No frequency response data available yet.
        </div>
    @else
        <div
            x-data="frdViewer({
                amplitude: @js($chartData),
                summed: @js($summedResponse),
                phase: @js($phaseData),
                showSummed: @js($showSummed),
            })"
            class="border border-base-300 rounded-box p-4 space-y-3 bg-base-100"
        >
            <div class="tabs tabs-box w-fit">
                <button type="button" class="tab" :class="tab === 'amplitude' && 'tab-active'"
                        @click="switchTo('amplitude')">Amplitude</button>
                <button type="button" class="tab" :class="tab === 'phase' && 'tab-active'"
                        @click="switchTo('phase')">Phase</button>
            </div>

            {{-- wire:ignore keeps Livewire from wiping the canvas Chart.js manages --}}
            <div wire:ignore class="relative h-80">
                <canvas x-ref="canvas"></canvas>
            </div>

            <p class="text-xs text-base-content/50">
                20&nbsp;Hz&nbsp;–&nbsp;20&nbsp;kHz (logarithmic).
                <span x-show="showSummed">Bold curve = complex sum of all drivers.</span>
            </p>
        </div>
    @endif
</div>
