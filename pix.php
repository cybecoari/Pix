<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerador de PIX</title>
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Biblioteca QRCode.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  
  <style>
    body {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      min-height: 100vh;
      padding-top: 20px;
    }
    .card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
    }
    .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: #0d6efd;
      color: #fff;
    }
    .input-group-text {
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #aaa;
    }
    #qrcode canvas {
      border-radius: 10px;
      padding: 10px;
      background: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body class="text-light">

  <div class="container py-5">
    <h2 class="text-center mb-4">
      <i class="bi bi-qr-code"></i> Gerador de QR Code PIX
    </h2>

    <div class="card shadow-lg p-4 mx-auto" style="max-width: 500px;">
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
        <input type="text" id="chave" class="form-control" placeholder="Chave PIX (CPF, CNPJ, Email, Telefone ou Chave Aleatória)" required />
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
        <input type="text" id="nome" class="form-control" placeholder="Nome do recebedor" required />
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
        <input type="text" id="cidade" class="form-control" placeholder="Cidade do recebedor" required />
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
        <input type="number" step="0.01" min="0" id="valor" class="form-control" placeholder="Valor (opcional)" />
      </div>

      <button onclick="gerarPIX()" id="gerarBtn" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-qr-code"></i> Gerar QR Code
      </button>

      <div id="qrcode" class="d-none text-center my-3"></div>
      <div class="dados d-none" id="dados"></div>

      <a href="/painel.php" class="btn btn-success w-100 mt-3">
        <i class="bi bi-speedometer2"></i> Voltar ao Painel
      </a>
    </div>
  </div>

  <!-- Áudios -->
  <audio id="somSucesso">
    <source src="https://cybercoari.com.br/cyber/audio/sucesso.mp3" type="audio/mpeg">
  </audio>
  <audio id="somErro">
    <source src="https://cybercoari.com.br/cyber/audio/erro.mp3" type="audio/mpeg">
  </audio>
  <audio id="somCopiado">
    <source src="https://cybercoari.com.br/cyber/audio/copiado.mp3" type="audio/mpeg">
  </audio>

  <script>
    // Elementos DOM
    const chaveInput = document.getElementById('chave');
    const nomeInput = document.getElementById('nome');
    const cidadeInput = document.getElementById('cidade');
    const valorInput = document.getElementById('valor');
    const gerarBtn = document.getElementById('gerarBtn');
    const qrcodeDiv = document.getElementById('qrcode');
    const dadosDiv = document.getElementById('dados');
    
    // Elementos de áudio
    const somSucesso = document.getElementById('somSucesso');
    const somErro = document.getElementById('somErro');
    const somCopiado = document.getElementById('somCopiado');
    
    // Função para tocar som
    function tocarSom(audioElement) {
      try {
        // Para o som se já estiver tocando
        audioElement.pause();
        audioElement.currentTime = 0;
        
        // Tenta tocar o som
        audioElement.play().catch(e => {
          console.log("Não foi possível tocar o som:", e.message);
          // Para navegadores mais restritivos, tentamos tocar sem interação do usuário
          // mas isso pode não funcionar em todos os casos
        });
      } catch (error) {
        console.log("Erro ao tentar tocar som:", error);
      }
    }
    
    // Pré-configuração dos áudios
    window.addEventListener('DOMContentLoaded', function() {
      // Configura volume
      somSucesso.volume = 0.7;
      somErro.volume = 0.7;
      somCopiado.volume = 0.7;
    });
    
    async function gerarPIX() {
      const chave = chaveInput.value.trim().replace(/\s/g, '');
      const nome = nomeInput.value.trim().replace(/\s+/g, ' ').toUpperCase();
      const cidade = cidadeInput.value.trim().replace(/\s+/g, ' ').toUpperCase();
      const valor = parseFloat(valorInput.value) || 0;

      // Validação dos campos
      if (!chave || !nome || !cidade) {
        // Toca som de erro
        tocarSom(somErro);
        
        Swal.fire({ 
          icon: "error", 
          title: "Erro", 
          text: "Preencha todos os campos obrigatórios.",
          confirmButtonText: "Entendi"
        });
        return;
      }

      try {
        // Atualiza botão
        gerarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Gerando...';
        gerarBtn.disabled = true;

        // Limpa resultados anteriores
        qrcodeDiv.innerHTML = "";
        qrcodeDiv.classList.add('d-none');
        dadosDiv.classList.add('d-none');

        // Gera payload PIX
        const payload = geraPayloadPIX(chave, nome, cidade, valor);

        // Gera QR Code
        new QRCode(qrcodeDiv, {
          text: payload,
          width: 256,
          height: 256,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });

        // Aguarda um pouco e aplica logo
        setTimeout(() => {
          const canvas = qrcodeDiv.querySelector('canvas');
          if (!canvas) return;

          const ctx = canvas.getContext('2d');
          const logo = new Image();
          logo.crossOrigin = "anonymous";
          
          // SUBSTITUA ESTE CAMINHO PELO SEU LOGO
          logo.src = '/assets/img/logo.png'; // ← Mude para o caminho do seu logo
          
          logo.onload = () => {
            const size = canvas.width * 0.2;
            const x = (canvas.width - size) / 2;
            const y = (canvas.height - size) / 2;
            
            // Desenha fundo branco para o logo
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(x - 4, y - 4, size + 8, size + 8);
            
            // Desenha o logo
            ctx.drawImage(logo, x, y, size, size);
            
            // Toca som de sucesso
            tocarSom(somSucesso);
            
            // Mostra resultado
            Swal.fire({
              icon: "success",
              title: "QR Code gerado!",
              text: "O QR Code PIX foi gerado com sucesso.",
              timer: 1500,
              showConfirmButton: false
            });
          };
          
          logo.onerror = () => {
            // Se o logo não carregar, apenas toca o som
            tocarSom(somSucesso);
            
            // Ainda mostra sucesso mesmo sem logo
            Swal.fire({
              icon: "success",
              title: "QR Code gerado!",
              text: "O QR Code PIX foi gerado (sem logo).",
              timer: 1500,
              showConfirmButton: false
            });
          };
        }, 300);

        // Mostra QR Code
        qrcodeDiv.classList.remove('d-none');

        // Cria área de dados com código PIX
        dadosDiv.innerHTML = `
          <hr class="my-4">
          <h5 class="mb-3"><i class="bi bi-code-slash"></i> Código PIX</h5>
          <div class="input-group mb-3">
            <textarea id="codigoPix" readonly class="form-control" rows="3" style="font-family: monospace; resize: none;">${payload}</textarea>
            <button onclick="copiarCodigo()" class="btn btn-outline-light" title="Copiar">
              <i class="bi bi-clipboard"></i>
            </button>
          </div>
          <div class="d-grid gap-2">
            <button onclick="copiarCodigo()" class="btn btn-outline-light">
              <i class="bi bi-clipboard"></i> Copiar Código PIX
            </button>
            <button onclick="baixarQR()" class="btn btn-warning">
              <i class="bi bi-download"></i> Baixar QR Code
            </button>
          </div>
        `;
        dadosDiv.classList.remove('d-none');

      } catch (error) {
        console.error("Erro ao gerar PIX:", error);
        // Toca som de erro
        tocarSom(somErro);
        
        Swal.fire({ 
          icon: "error", 
          title: "Erro", 
          text: "Ocorreu um erro ao gerar o QR Code PIX. Verifique os dados e tente novamente.",
          confirmButtonText: "Entendi"
        });
      } finally {
        // Restaura botão
        gerarBtn.innerHTML = '<i class="bi bi-qr-code"></i> Gerar QR Code';
        gerarBtn.disabled = false;
      }
    }

    function geraPayloadPIX(chave, nome, cidade, valor) {
      function add(tag, valor) {
        valor = String(valor);
        return tag + valor.length.toString().padStart(2, '0') + valor;
      }

      // Formatação dos dados conforme padrão PIX
      const gui = add('00', 'BR.GOV.BCB.PIX');
      const chavePix = add('01', chave);
      const merchantAccount = add('26', gui + chavePix);
      const merchantCategoryCode = '52040000'; // Código para "Transferência PIX"
      const transactionCurrency = '5303986'; // BRL
      const txValue = valor > 0 ? add('54', valor.toFixed(2)) : '';
      const countryCode = '5802BR';
      const merchantName = add('59', nome.substring(0, 25));
      const merchantCity = add('60', cidade.substring(0, 15));
      const txid = add('05', '***');
      const additionalDataField = add('62', txid);

      // Monta payload sem CRC
      let payloadSemCRC = '000201' + merchantAccount + merchantCategoryCode +
        transactionCurrency + txValue + countryCode + merchantName +
        merchantCity + additionalDataField;

      // Adiciona CRC
      payloadSemCRC += '6304';
      return payloadSemCRC + geraCRC16(payloadSemCRC);
    }

    function geraCRC16(payload) {
      let crc = 0xFFFF;
      for (let i = 0; i < payload.length; i++) {
        crc ^= payload.charCodeAt(i) << 8;
        for (let j = 0; j < 8; j++) {
          if (crc & 0x8000) {
            crc = (crc << 1) ^ 0x1021;
          } else {
            crc <<= 1;
          }
          crc &= 0xFFFF;
        }
      }
      return crc.toString(16).toUpperCase().padStart(4, '0');
    }

    async function copiarCodigo() {
      try {
        const codigo = document.getElementById("codigoPix");
        await navigator.clipboard.writeText(codigo.value);
        
        // Toca som de copiado
        tocarSom(somCopiado);
        
        // Feedback visual
        Swal.fire({
          icon: "success",
          title: "Copiado!",
          text: "Código PIX copiado para a área de transferência.",
          timer: 1500,
          showConfirmButton: false
        });
      } catch (error) {
        console.error("Erro ao copiar:", error);
        tocarSom(somErro);
        Swal.fire({
          icon: "error",
          title: "Erro ao copiar",
          text: "Não foi possível copiar o código.",
          confirmButtonText: "Entendi"
        });
      }
    }

    function baixarQR() {
      const canvas = qrcodeDiv.querySelector("canvas");
      if (!canvas) {
        tocarSom(somErro);
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: "QR Code não encontrado.",
          confirmButtonText: "Entendi"
        });
        return;
      }
      
      try {
        // Cria link para download
        const link = document.createElement("a");
        link.href = canvas.toDataURL("image/png");
        link.download = `pix-qrcode-${Date.now()}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Toca som de sucesso
        tocarSom(somSucesso);
        
        // Feedback
        Swal.fire({
          icon: "success",
          title: "Download iniciado!",
          text: "QR Code baixado com sucesso.",
          timer: 1500,
          showConfirmButton: false
        });
      } catch (error) {
        console.error("Erro ao baixar:", error);
        tocarSom(somErro);
        Swal.fire({
          icon: "error",
          title: "Erro ao baixar",
          text: "Não foi possível baixar o QR Code.",
          confirmButtonText: "Entendi"
        });
      }
    }
    
    // Validação em tempo real
    chaveInput.addEventListener('input', function() {
      this.value = this.value.trim();
    });
    
    nomeInput.addEventListener('input', function() {
      this.value = this.value.toUpperCase();
    });
    
    cidadeInput.addEventListener('input', function() {
      this.value = this.value.toUpperCase();
    });
    
    // Tecla Enter para gerar
    document.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        gerarPIX();
      }
    });
  </script>
</body>
</html>