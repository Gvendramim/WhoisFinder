jQuery(document).ready(function ($) {
    const $ipInfo = $('#wf-ip-info');
    const $userIP = $('#wf-user-ip');
    const $whoisInfo = $('#wf-whois-info');
    const $domainInput = $('#wf-domain-input');
    const $buscarBtn = $('#wf-buscar-btn');

    function showLoading(target) {
        target.html('<p>Carregando...</p>');
    }

    function fetchIPDetails() {
        showLoading($ipInfo);

        $.post(wf_ajax_object.ajax_url, {
            action: 'wf_fetch_ip_details',
            nonce: wf_ajax_object.nonce
        })
        .done(response => {
            if (response.success) {
                const { ip, country, city, isp, latitude, longitude } = response.data;
                $userIP.text(ip);
                $ipInfo.html(`
                    <p><strong>País:</strong> ${country}</p>
                    <p><strong>Cidade:</strong> ${city}</p>
                    <p><strong>Provedor:</strong> ${isp}</p>
                    <p><strong>Latitude:</strong> ${latitude}</p>
                    <p><strong>Longitude:</strong> ${longitude}</p>
                `);
            } else {
                $ipInfo.html('<p>Não foi possível carregar as informações do IP.</p>');
            }
        })
        .fail(() => {
            $ipInfo.html('<p>Erro na requisição. Tente novamente mais tarde.</p>');
        });
    }

    $buscarBtn.on('click', function () {
        const domain = $domainInput.val().trim();
        
        if (!domain) {
            alert('Por favor, insira um domínio válido.');
            return;
        }

        showLoading($whoisInfo);

        $.post(wf_ajax_object.ajax_url, {
            action: 'wf_fetch_whois_info',
            nonce: wf_ajax_object.nonce,
            domain: domain
        })
        .done(response => {
            if (response.success) {
                const { domainName, createdDate, updatedDate, whoisServer, status } = response.data;
                $whoisInfo.html(`
                    <p><strong>Domínio:</strong> ${domainName}</p>
                    <p><strong>Data de Criação:</strong> ${createdDate || 'N/A'}</p>
                    <p><strong>Última Atualização:</strong> ${updatedDate || 'N/A'}</p>
                    <p><strong>Servidor Whois:</strong> ${whoisServer || 'N/A'}</p>
                    <p><strong>Status:</strong> ${status || 'N/A'}</p>
                `);
            } else {
                $whoisInfo.html('<p>Não foi possível obter informações Whois.</p>');
            }
        })
        .fail(() => {
            $whoisInfo.html('<p>Erro na requisição. Tente novamente mais tarde.</p>');
        });
    });

    fetchIPDetails();
});
