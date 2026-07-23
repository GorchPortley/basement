import Chart from 'chart.js/auto'

// Expose Chart globally in case other views want it.
window.Chart = Chart

const TICKS = [20, 50, 100, 200, 500, 1000, 2000, 5000, 10000, 20000]

const lineDataset = (d) => ({
    label: d.label,
    data: d.data,
    borderColor: d.borderColor,
    borderWidth: 1.5,
    pointRadius: 0,
    fill: false,
    tension: 0.2,
})

/**
 * Alpine component backing the FrequencyResponseViewer Livewire component.
 * Draws amplitude or phase curves on a shared logarithmic frequency axis.
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('frdViewer', (config) => ({
        tab: 'amplitude',
        showSummed: config.showSummed,
        chart: null,

        init() {
            this.draw()
        },

        switchTo(tab) {
            this.tab = tab
            this.draw()
        },

        datasets() {
            if (this.tab === 'phase') {
                return config.phase.map(lineDataset)
            }

            const sets = config.amplitude.map(lineDataset)

            if (config.showSummed && config.summed.length) {
                sets.push({
                    label: 'System (summed)',
                    data: config.summed,
                    borderColor: '#111827',
                    borderWidth: 3,
                    pointRadius: 0,
                    fill: false,
                    tension: 0.2,
                })
            }

            return sets
        },

        draw() {
            if (this.chart) this.chart.destroy()

            this.chart = new Chart(this.$refs.canvas.getContext('2d'), {
                type: 'line',
                data: { datasets: this.datasets() },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'nearest', intersect: false },
                    scales: {
                        x: {
                            type: 'logarithmic',
                            min: 20,
                            max: 20000,
                            title: { display: true, text: 'Frequency (Hz)' },
                            ticks: {
                                callback: (v) => (TICKS.includes(v) ? v : ''),
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: this.tab === 'phase' ? 'Phase (°)' : 'SPL (dB)',
                            },
                        },
                    },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } },
                    },
                },
            })
        },
    }))

    // Generic Chart.js renderer: pass a full Chart.js config from the server.
    window.Alpine.data('chart', (config) => ({
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.canvas.getContext('2d'), config)
        },
    }))
})
