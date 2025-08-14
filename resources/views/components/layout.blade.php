<!DOCTYPE html>
<html lang="fr">
<head>
    <title>{{ $title ?? 'Frise Chronologique Interactive' }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="font-sans">

<!-- Notification -->
<x-notification/>

{{ $slot  }}

<script>
    document.addEventListener('alpine:init', () => {
        // Magic: $tooltip
        Alpine.magic('tooltip', el => message => {
            let instance = window.tippy(el, {content: message, trigger: 'manual', theme: 'light'})

            instance.show()

            setTimeout(() => {
                instance.hide()

                setTimeout(() => instance.destroy(), 150)
            }, 2000)
        })

        // Directive: x-tooltip
        // noinspection GrazieInspection
        Alpine.directive('tooltip', (el, {expression}, {evaluateLater, effect}) => {
            // Function to evaluate expression reactively, the message will change when the evaluated value changes
            // See: https://alpinejs.dev/advanced/extending#introducing-reactivity
            let showTooltip = evaluateLater(expression);
            effect(() => {
                showTooltip(message => {
                    window.tippy(el, {
                        content: message,
                        theme: 'light',
                        arrow: false,
                        animation: 'scale-subtle',
                        touch: 'hold'
                    })
                })
            });
        })
    })
</script>

</body>
</html>
