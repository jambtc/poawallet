console.log('[accountValue array]',yiiUserOptions.accountValueArray);

$(".accountValue").sparkline(yiiUserOptions.accountValueArray, {
        type: 'line',
        width: '100%',
        height: '80',
        barColor: '#72a1ec',
        lineColor: '#6164c1',
        fillColor: 'rgba(97, 100, 193, 0.3)',
        highlightLineColor: 'rgba(0,0,0,.1)',
        highlightSpotColor: 'rgba(0,0,0,.2)'
});
