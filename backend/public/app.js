// Charge les données à partir du backend
d3.json("/events").then(events => {
    // Convertit les dates en objets JavaScript Date
    events.forEach(d => {
        d.date = new Date(d.date);
    });

    // Crée la visualisation initiale avec les données chargées
    createTimeline(events);
}).catch(error => {
    console.error("Erreur lors du chargement des événements:", error);
});

// Redimensionne le canevas lorsque la fenêtre est redimensionnée
window.addEventListener("resize", () => {
    createTimeline();
});

// Définit la nomenclature des dates
const dateFormat = d3.timeFormatDefaultLocale({
    "dateTime": "%A %e %B %Y, %X",
    "date": "%d/%m/%Y",
    "time": "%H:%M:%S",
    "periods": ["AM", "PM"],
    "days": ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
    "shortDays": ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
    "months": ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
    "shortMonths": ["janv.", "févr.", "mars", "avr.", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc."]
});

// Fonction pour créer ou mettre à jour la visualisation
function createTimeline(events) {
    // Sélectionne l'élément div avec l'id 'timeline'
    const timeline = d3.select("#timeline");

    // Supprime l'ancien SVG s'il existe
    timeline.select("svg").remove();

    // Définit les dimensions du canevas en fonction de la taille de la fenêtre
    const width = window.innerWidth - 20;
    const height = window.innerHeight - 20;

    // Définit l'intervalle de temps pour la frise chronologique d'arrivée sur la page
    const startDate = new Date("1020-01-01");
    const endDate = new Date("2030-12-31");

    // Ajoute un élément SVG au div 'timeline', il contiendra tous les éléments de la frise chronologique
    const svg = timeline.append("svg")
        .attr("width", width)
        .attr("height", height)
        .call(d3.zoom().on("zoom", zoomed));

    // Ajoute un groupe "graduation" pour contenir les éléments de la frise en elle même
    const graduation = svg.append("g")
        .attr("transform", `translate(40, ${height / 2})`);

    // Ajoute un groupe "eventsGroup" pour contenir les éléments de la frise en elle même
    const eventsGroup = svg.append("g")
        .attr("transform", `translate(40, ${height / 2})`);

    // Exemple d'ajout d'une ligne pour la frise chronologique
    graduation.append("line")
        .attr("x1", 0)
        .attr("x2", width - 80)
        .attr("y1", 0)
        .attr("y2", 0)
        .attr("stroke", "#000")
        .attr("stroke-width", 2);

    // Calcule l'échelle de temps
    const timeScale = d3.scaleTime()
        .domain([startDate, endDate])
        .range([0, width - 80]);
    let displayedScale = timeScale;

    // Fonction pour ajouter des graduations avec trois types de ticks
    function addTicks(scale) {
        const domain = scale.domain();
        const range = scale.range();
        const duration = domain[1] - domain[0];
        const rangeDuration = range[1] - range[0];
        const pixelsPerDay = rangeDuration / (duration / (24 * 60 * 60 * 1000));
        let majorTicks, minorTicks;
        if (pixelsPerDay < 0.0002) {
            // work in progress
            // Affichage dynamique pour les périodes au dessus de l'ordre du millénaire
            frequenceMajorTicks = Math.pow(10,Math.round(Math.log10(0.2/pixelsPerDay)));
            frequenceMinorTicks = frequenceMajorTicks / 10;
            majorTicks = d3.timeYear.every(frequenceMajorTicks);
            minorTicks = d3.timeYear.every(frequenceMinorTicks);
        } else if (pixelsPerDay < 0.0008) {
            // Afficher des graduations pour les 5 siècles
            majorTicks = d3.timeYear.every(500);
            minorTicks = d3.timeYear.every(100);
        } else if (pixelsPerDay < 0.002) {
            // Afficher des graduations pour les siècles
            majorTicks = d3.timeYear.every(100);
            minorTicks = d3.timeYear.every(50);
        } else if (pixelsPerDay < 0.009) {
            // Afficher des graduations pour les demis siècles
            majorTicks = d3.timeYear.every(50);
            minorTicks = d3.timeYear.every(10);
        } else if (pixelsPerDay < 0.2) {
            // Afficher des graduations pour les décennies
            majorTicks = d3.timeYear.every(10);
            minorTicks = d3.timeYear.every(1);
        } else if (pixelsPerDay < 3) {
            // Afficher des graduations pour les années
            majorTicks = d3.timeYear.every(1);
            minorTicks = d3.timeMonth.every(1);
        } else if (pixelsPerDay < 17) {
            // Afficher des graduations pour les mois
            majorTicks = d3.timeMonth.every(1);
            minorTicks = d3.timeMonday.every(1);
        } else if (pixelsPerDay < 100) {
            // Afficher des graduations pour les semaines
            majorTicks = d3.timeMonday.every(1);
            minorTicks = d3.timeDay.every(1);
        } else if (pixelsPerDay < 370) {
            // Afficher des graduations pour les jours
            majorTicks = d3.timeDay.every(1);
            minorTicks = d3.timeHour.every(12);
        } else {
            // Afficher des graduations pour les heures
            majorTicks = d3.timeDay.every(1);
            minorTicks = d3.timeHour.every(1);
        }

        // Ajoute des graduations secondaires (grises sans étiquettes)
        graduation.selectAll("line.minor-tick")
            .data(minorTicks.range(...domain))
            .join(
                enter => enter.append("line")
                    .attr("class", "minor-tick")
                    .attr("x1", d => scale(d))
                    .attr("x2", d => scale(d))
                    .attr("y1", -5)
                    .attr("y2", 5)
                    .attr("stroke", "#ccc")
                    .attr("stroke-width", 1),
                update => update
                    .attr("x1", d => scale(d))
                    .attr("x2", d => scale(d)),
                exit => exit.remove()
            );

        // Ajoute des graduations principales (noires avec étiquettes)
        graduation.selectAll("line.major-tick")
            .data(majorTicks.range(...domain))
            .join(
                enter => enter.append("line")
                    .attr("class", "major-tick")
                    .attr("x1", d => scale(d))
                    .attr("x2", d => scale(d))
                    .attr("y1", -10)
                    .attr("y2", 10)
                    .attr("stroke", "#000")
                    .attr("stroke-width", 1),
                update => update
                    .attr("x1", d => scale(d))
                    .attr("x2", d => scale(d)),
                exit => exit.remove()
            );

        // Ajoute des étiquettes pour les graduations principales
        graduation.selectAll("text.major-label")
            .data(majorTicks.range(...domain))
            .join(
                enter => enter.append("text")
                    .attr("class", "major-label")
                    .attr("x", d => scale(d))
                    .attr("y", 20)
                    .attr("text-anchor", "middle")
                    .attr("font-size", "12px")
                    .text(d => formatDate(d, scale)),
                update => update
                    .attr("x", d => scale(d))
                    .text(d => formatDate(d, scale)),
                exit => exit.remove()
            );

        // if hours are displayed, add graduations labels for minor ticks
        if (pixelsPerDay >= 800) {
            // Ajoute des étiquettes pour les graduations principales
            graduation.selectAll("text.minor-label")
                .data(minorTicks.range(...domain))
                .join(
                    enter => enter.append("text")
                        .attr("class", "minor-label")
                        .attr("x", d => scale(d))
                        .attr("y", -20)
                        .attr("text-anchor", "middle")
                        .attr("font-size", "12px")
                        .text(d => formatHour(d, scale)),
                    update => update
                        .attr("x", d => scale(d))
                        .text(d => formatHour(d, scale)),
                    exit => exit.remove()
                );

        }
        // remove them otherwise
        else {
            graduation.selectAll("text.minor-label").remove();
        }



    }

    // Ajoute des graduations avec trois types de ticks
    addTicks(timeScale);

    if (events) {
        // Ajoute des rectangles pour chaque événement
        eventsGroup.selectAll("rect.event")
            .data(events)
            .enter()
            .append("rect")
            .attr("class", "event")
            .attr("x", d => timeScale(d.date) - 25)
            .attr("y", -30)
            .attr("width", 50)
            .attr("height", 20)
            .attr("fill", "steelblue")
            .on("click", function(event, d) {
                // Toggle the expanded state
                d.expanded = !d.expanded;

                if (d.expanded) {
                    //supprime l'eventuel label
                    eventsGroup.selectAll("text.event-label")
                        .data(events.filter(d => !d.expanded))
                        .exit()
                        .remove();

                    // Agrandit le rectangle et affiche le formulaire
                    d3.select(this)
                        .attr("width", 300)
                        .attr("height", 600)
                        .attr("fill", "orange");

                    // Charge le formulaire depuis le fichier Blade
                    fetch(`/eventForm?id=${d.id}&name=${d.name}&description=${d.description}&date=${d.date.toISOString().split('T')[0]}`)
                        .then(response => response.text())
                        .then(formHtml => {
                            // Ajoute ou met à jour le formulaire
                            eventsGroup.selectAll("foreignObject.eventForm")
                                .data([d])
                                .join(
                                    enter => enter.append("foreignObject")
                                        .attr("class", "eventForm")
                                        .attr("x", timeScale(d.date))
                                        .attr("y", -30)
                                        .attr("width", 300)
                                        .attr("height", 600)
                                        .append("xhtml:div")
                                        .html(formHtml),
                                    update => update
                                        .attr("x", timeScale(d.date) - 150)
                                        .html(formHtml),
                                    exit => exit.remove()
                                );

                            // Ajoute l'écouteur d'événement pour le formulaire
                            document.getElementById('eventForm').addEventListener('submit', async (e) => {
                                e.preventDefault();
                                console.log('Submitting form data:', e.target);
                                const id = e.target['event-id'].value;
                                const formData = {
                                    name: e.target.name.value,
                                    description: e.target.description.value,
                                    date: e.target.date.value
                                };
                                const response = await fetch('/events/' + id, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify(formData)
                                });
                                if (response.ok) {
                                    console.log('Event updated:', await response.json());
                                    // TODO: Add the new event to the timeline
                                } else {
                                    console.error('Failed to update event:', await response.text());
                                }
                            });

                            // Ajoute l'écouteur d'événement pour le bouton de suppression
                            document.getElementById('deleteEventButton').addEventListener('click', async () => {
                                const id = d.id;
                                const response = await fetch('/events/' + id, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                });
                                if (response.ok) {
                                    console.log('Event deleted:', await response.json());
                                    // Supprime l'événement de la frise chronologique
                                    events = events.filter(event => event.id !== id);
                                    createTimeline(events);
                                } else {
                                    console.error('Failed to delete event:', await response.text());
                                }
                            });
                        });

                } else {
                    // Réduit le rectangle et cache le formulaire
                    d3.select(this)
                        .attr("width", 50)
                        .attr("height", 20)
                        .attr("fill", "steelblue");

                    // Supprime le formulaire
                    eventsGroup.selectAll("foreignObject.eventForm").remove();

                    // Ajoute des étiquettes de texte
                    eventsGroup.selectAll("text.event-label")
                        .data(events.filter(d => !d.expanded))
                        .enter()
                        .append("text")
                        .attr("class", "event-label")
                        .attr("x", d => timeScale(d.date))
                        .attr("y", -16)
                        .attr("text-anchor", "middle")
                        .attr("font-size", "12px")
                        .text(d => d.name);
                }
            });

        // Ajoute des étiquettes de texte pour chaque événement non étendu
        eventsGroup.selectAll("text.event-label")
            .data(events.filter(d => !d.expanded))
            .enter()
            .append("text")
            .attr("class", "event-label")
            .attr("x", d => timeScale(d.date))
            .attr("y", -16)
            .attr("text-anchor", "middle")
            .attr("font-size", "12px")
            .text(d => d.name);
    }

    // Fonction de zoom
    function zoomed(event) {
        const newXScale = event.transform.rescaleX(timeScale);

        // Met à jour les graduations avec trois types de ticks
        addTicks(newXScale);

        // Met à jour les rectangles d'événements
        eventsGroup.selectAll("rect.event")
            .attr("x", d => newXScale(d.date) - 25);

        // Met à jour les étiquettes d'événements
        eventsGroup.selectAll("text.event-label")
            .attr("x", d => newXScale(d.date));

        // Met à jour les formulaires d'événements
        eventsGroup.selectAll("foreignObject.eventForm")
            .attr("x", d => newXScale(d.date));

        displayedScale = newXScale; // Update the global displayedScale with the rescaled version
    }

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

    // Ajoute l'écouteur d'événement pour le clic droit
    svg.on("contextmenu", function(event) {
        event.preventDefault();
        const [x, y] = d3.pointer(event);
        const date = displayedScale.invert(x - 40); // Convertit la position x en date

        // Charge le formulaire depuis le fichier Blade
        fetch(`/eventForm?date=${date.toISOString().split('T')[0]}&title=Créer un événement&descriptionText=oui oui.`)
            .then(response => response.text())
            .then(formHtml => {
                // Ajoute ou met à jour le formulaire
                eventsGroup.selectAll("foreignObject.createEventForm")
                    .data([date])
                    .join(
                        enter => enter.append("foreignObject")
                            .attr("class", "createEventForm")
                            .attr("x", x - 150)
                            .attr("y", - 120)
                            .attr("width", 300)
                            .attr("height", 600)
                            .append("xhtml:div")
                            .html(formHtml),
                        update => update
                            .attr("x", x - 150)
                            .attr("y", - 120)
                            .html(formHtml),
                        exit => exit.remove()
                    );

                // Ajoute l'écouteur d'événement pour le formulaire
                document.getElementById('eventForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const response = await fetch('/events', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });
                    console.log('Submitting form data:', formData);
                    console.log('Response:', response);
                    if (response.ok) {
                        const updatedEvents = await response.json();
                        createTimeline(updatedEvents);
                    } else {
                        console.error('Failed to create event:', await response.text());
                    }
                });
            });
    });
}
