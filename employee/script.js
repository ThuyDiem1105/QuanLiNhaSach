
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.sidebar-link');
    const frame = document.getElementById('contentFrame');

    const pageMap = {
        profileBtn: 'profile.php',
        scheduleBtn: 'schedule.php'
    };

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            if (link.id === 'logoutBtn') {
                return; 
            }
            e.preventDefault();

            links.forEach(item => item.classList.remove('active'));
            link.classList.add('active');

            const page = pageMap[link.id];
            if (page) {
                frame.src = page;
            }
        });
    });
});