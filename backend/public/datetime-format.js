// Fonction pour formater les dates en fonction de l'échelle de temps
function formatDate(date, scale) {
    const domain = scale.domain();
    const range = scale.range();
    const duration = domain[1] - domain[0];
    const rangeDuration = range[1] - range[0];
    const pixelsPerDay = rangeDuration / (duration / (24 * 60 * 60 * 1000));

    if (pixelsPerDay < 3) {
        return date.getFullYear();
    } else if (pixelsPerDay < 17) {
        return d3.timeFormat("%B %Y")(date);
    } else if (pixelsPerDay < 100) {
        return d3.timeFormat("%d %B %Y")(date);
    } else {
        return d3.timeFormat("%A %d %B %Y")(date);
    }
}

// Fonction pour formater les dates en fonction de l'échelle de temps
function formatHour(date, scale) {
    return d3.timeFormat("%H:%M")(date);
}
