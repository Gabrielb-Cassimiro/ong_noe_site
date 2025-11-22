// Acessibilidade que tá la no pdf
function toggleContrast() { document.body.classList.toggle('high-contrast'); }
function toggleFontSize() { document.body.classList.toggle('font-large'); }

// Carrossel (não mexe que isso aqui é muito chato)
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.carousel-inner');
    if(track) {
        let index = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const total = slides.length;
        function move(dir) { index = (index + dir + total) % total; track.style.transform = `translateX(-${index*100}%)`; }
        document.querySelector('.next').addEventListener('click', ()=>move(1));
        document.querySelector('.prev').addEventListener('click', ()=>move(-1));
        setInterval(()=>move(1), 5000);
    }
    
    // API VIA CEP 
    const cepInput = document.querySelector('input[name="cep"]');
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.erro) {
                            // Preenchimento automático
                            document.querySelector('input[name="logradouro"]').value = data.logradouro;
                            document.querySelector('input[name="bairro"]').value = data.bairro;
                            document.querySelector('input[name="cidade"]').value = data.localidade;
                            document.querySelector('input[name="estado"]').value = data.uf;
                        } else {
                            // Feedback discreto no console ou borda vermelha (sem alert)
                            this.style.borderColor = 'red';
                        }
                    });
            }
        });
    }
});

// Validação Elegante que ta no pdf (Feedback sem Alert)
function validarCadastro(event) {
    const s1 = document.getElementById('senha').value;
    const s2 = document.getElementById('confirma_senha').value;
    const login = document.getElementById('login').value;
    
    const msgDiv = document.getElementById('msg-js'); // Div obrigatória no HTML !!!!!!!!
    msgDiv.innerHTML = ''; 
    msgDiv.style.display = 'none';
    let erros = [];

    // Regra 8: Senhas iguais 
    if (s1 !== s2) {
        erros.push('As senhas não coincidem.');
    }
    
    // Regra 6: Login exatos 6 chars 
    if (login.length !== 6) {
        erros.push('O login deve ter exatamente 6 caracteres.');
    }

    if (erros.length > 0) {
        event.preventDefault(); // Impede envio
        msgDiv.innerHTML = '<div class="alert alert-danger">' + erros.join('<br>') + '</div>';
        msgDiv.style.display = 'block';
        window.scrollTo(0,0);
        return false;
    }
    return true;
}