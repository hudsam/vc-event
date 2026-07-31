$(document).ready(function () {
    const API_URL = 'YOUR_API_URL/api';

    // Fetch Events on load
    fetchEvents();

    function fetchEvents() {
        $.ajax({
            url: API_URL + '/events',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                $('#loading').hide();
                renderEvents(response.data);
            },
            error: function (xhr, status, error) {
                $('#loading').html('<div class="alert alert-danger">Gagal memuat event. Pastikan server API berjalan.</div>');
                console.error("Error fetching events:", error);
            }
        });
    }

    function renderEvents(events) {
        const container = $('#event-list');
        container.empty();

        if (!events || events.length === 0) {
            container.html('<div class="col-12 text-center"><p>Belum ada event yang tersedia.</p></div>');
            return;
        }

        events.forEach(event => {
            const date = new Date(event.event_date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const card = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 event-card shadow-sm">
                        <img src="${event.banner_url ? event.banner_url : 'https://placehold.co/600x400?text=Event+Poster'}" class="card-img-top" alt="${event.title}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${event.title}</h5>
                            <p class="card-text text-muted mb-2"><small><i class="bi bi-calendar"></i> ${date}</small></p>
                            <p class="card-text">${event.description || 'Deskripsi singkat belum tersedia.'}</p>
                            <div class="mt-auto">
                                <a href="#" class="btn btn-outline-primary w-100">Detail Event</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }
});
