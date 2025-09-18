// Funcionalidades principais do site da ONG de Noé

document.addEventListener('DOMContentLoaded', function() {
    initializeAccessibility();
    initializeForms();
    initializeModals();
    initializeAnimations();
});

// Funcionalidades de Acessibilidade
function initializeAccessibility() {
    // Controle de contraste
    const contrastBtn = document.getElementById('contrast-toggle');
    if (contrastBtn) {
        contrastBtn.addEventListener('click', toggleContrast);
        
        // Carrega preferência salva
        if (localStorage.getItem('high-contrast') === 'true') {
            document.body.classList.add('high-contrast');
        }
    }
    
    // Controle de tamanho de fonte
    const fontSizeControls = document.querySelectorAll('[data-font-action]');
    fontSizeControls.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.fontAction;
            changeFontSize(action);
        });
    });
    
    // Carrega tamanho de fonte salvo
    const savedFontSize = localStorage.getItem('font-size');
    if (savedFontSize) {
        document.body.className = document.body.className.replace(/font-\w+/g, '');
        document.body.classList.add(savedFontSize);
    }
}

function toggleContrast() {
    document.body.classList.toggle('high-contrast');
    const isHighContrast = document.body.classList.contains('high-contrast');
    localStorage.setItem('high-contrast', isHighContrast);
}

function changeFontSize(action) {
    const currentClasses = document.body.classList;
    const fontClasses = ['font-small', 'font-large', 'font-xl'];
    
    // Remove classes de fonte existentes
    fontClasses.forEach(cls => currentClasses.remove(cls));
    
    let newClass = '';
    switch(action) {
        case 'increase':
            if (currentClasses.contains('font-small')) {
                newClass = '';
            } else if (!currentClasses.contains('font-large') && !currentClasses.contains('font-xl')) {
                newClass = 'font-large';
            } else if (currentClasses.contains('font-large')) {
                newClass = 'font-xl';
            }
            break;
        case 'decrease':
            if (currentClasses.contains('font-xl')) {
                newClass = 'font-large';
            } else if (currentClasses.contains('font-large')) {
                newClass = '';
            } else {
                newClass = 'font-small';
            }
            break;
        case 'reset':
            newClass = '';
            break;
    }
    
    if (newClass) {
        document.body.classList.add(newClass);
        localStorage.setItem('font-size', newClass);
    } else {
        localStorage.removeItem('font-size');
    }
}

// Funcionalidades de Formulários
function initializeForms() {
    // Validação em tempo real
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });
        
        form.addEventListener('submit', handleFormSubmit);
    });
    
    // Máscara para CPF
    const cpfInputs = document.querySelectorAll('input[name="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', applyCPFMask);
    });
    
    // Máscara para telefone
    const phoneInputs = document.querySelectorAll('input[name="telefone_celular"], input[name="telefone_fixo"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', applyPhoneMask);
    });
    
    // Máscara para CEP
    const cepInputs = document.querySelectorAll('input[name="cep"]');
    cepInputs.forEach(input => {
        input.addEventListener('input', applyCEPMask);
        input.addEventListener('blur', fetchAddressByCEP);
    });
}

function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    const fieldName = field.name;
    
    clearFieldError(event);
    
    // Validações específicas
    switch(fieldName) {
        case 'nome':
            if (value.length < 15 || value.length > 80) {
                showFieldError(field, 'Nome deve ter entre 15 e 80 caracteres');
                return false;
            }
            if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(value)) {
                showFieldError(field, 'Nome deve conter apenas letras');
                return false;
            }
            break;
            
        case 'cpf':
            if (!validateCPF(value)) {
                showFieldError(field, 'CPF inválido');
                return false;
            }
            break;
            
        case 'email':
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                showFieldError(field, 'Email inválido');
                return false;
            }
            break;
            
        case 'login':
            if (value.length !== 6 || !/^[a-zA-Z]+$/.test(value)) {
                showFieldError(field, 'Login deve ter exatamente 6 caracteres alfabéticos');
                return false;
            }
            break;
            
        case 'senha':
            if (value.length < 8) {
                showFieldError(field, 'Senha deve ter pelo menos 8 caracteres');
                return false;
            }
            break;
            
        case 'confirma_senha':
            const senhaField = document.querySelector('input[name="senha"]');
            if (senhaField && value !== senhaField.value) {
                showFieldError(field, 'Senhas não coincidem');
                return false;
            }
            break;
    }
    
    field.classList.add('success');
    return true;
}

function clearFieldError(event) {
    const field = event.target;
    field.classList.remove('error', 'success');
    const errorMsg = field.parentNode.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

function showFieldError(field, message) {
    field.classList.add('error');
    field.classList.remove('success');
    
    // Remove mensagem anterior se existir
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Adiciona nova mensagem
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = 'var(--error-red)';
    errorDiv.style.fontSize = 'var(--font-size-small)';
    errorDiv.style.marginTop = 'var(--spacing-xs)';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField({ target: input })) {
            isValid = false;
        }
    });
    
    if (isValid) {
        showLoading(form);
        // Aqui seria feita a submissão real do formulário
        setTimeout(() => {
            hideLoading(form);
            showAlert('Formulário enviado com sucesso!', 'success');
        }, 2000);
    } else {
        showAlert('Por favor, corrija os erros no formulário', 'error');
    }
}

// Máscaras de entrada
function applyCPFMask(event) {
    let value = event.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    event.target.value = value;
}

function applyPhoneMask(event) {
    let value = event.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    event.target.value = value;
}

function applyCEPMask(event) {
    let value = event.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
    event.target.value = value;
}

// Validação de CPF
function validateCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Busca de endereço por CEP
async function fetchAddressByCEP(event) {
    const cepField = event.target;
    const cep = cepField.value.replace(/\D/g, '');
    
    if (cep.length !== 8) return;
    
    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();
        
        if (!data.erro) {
            // Preenche os campos de endereço
            const fields = {
                'logradouro': data.logradouro,
                'bairro': data.bairro,
                'cidade': data.localidade,
                'estado': data.uf
            };
            
            Object.keys(fields).forEach(fieldName => {
                const field = document.querySelector(`input[name="${fieldName}"]`);
                if (field && fields[fieldName]) {
                    field.value = fields[fieldName];
                }
            });
        }
    } catch (error) {
        console.log('Erro ao buscar CEP:', error);
    }
}

// Funcionalidades de Modal
function initializeModals() {
    // Botões que abrem modais
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            openModal(modalId);
        });
    });
    
    // Botões que fecham modais
    const modalClosers = document.querySelectorAll('[data-modal-close]');
    modalClosers.forEach(closer => {
        closer.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });
    
    // Fechar modal clicando no fundo
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Animações
function initializeAnimations() {
    // Observador para animações de entrada
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    });
    
    // Observa elementos que devem ser animados
    const animatedElements = document.querySelectorAll('.card, .product-card');
    animatedElements.forEach(el => observer.observe(el));
}

// Utilitários
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Remove alertas existentes
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Adiciona o novo alerta
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showLoading(element) {
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'loading';
    loadingDiv.id = 'loading-indicator';
    
    const submitBtn = element.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.appendChild(loadingDiv);
    }
}

function hideLoading(element) {
    const loadingDiv = element.querySelector('#loading-indicator');
    if (loadingDiv) {
        loadingDiv.remove();
    }
    
    const submitBtn = element.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
    }
}

// Função para confirmar exclusão
function confirmDelete(message = 'Tem certeza que deseja excluir este item?') {
    return confirm(message);
}

// Função para logout
function logout() {
    if (confirm('Tem certeza que deseja sair?')) {
        window.location.href = 'login.php';
    }
}

