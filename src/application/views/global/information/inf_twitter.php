<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
    new TWTR.Widget({
        version: 2,
        type: 'profile',
        rpp: 4,
        interval: 30000,
        width: 250,
        height: 300,
        theme: {
            shell: {
                background: '#ffffff',
                color: '#333333'
            },
            tweets: {
                background: '#ededed',
                color: '#333333',
                links: '#122182'
            }
        },
        features: {
            scrollbar: false,
            loop: false,
            live: false,
            behavior: 'all'
        }
    }).render().setUser('euroharmony').start();
</script>