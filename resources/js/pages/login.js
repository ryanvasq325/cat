import Requests from "../components/requests.js";

const btnLogin     = document.getElementById('Login');
const btnCadastrar = document.getElementById('Cadastrar');

btnLogin.addEventListener('click', async () => {
    try {
        const response = await new Requests()
            .setForm('form')
            .post('/auth');

        if (!response.status) {
            Swal.fire({ title: "Atenção!", text: response.msg, icon: "error", timer: 3000 });
            return;
        }

        Swal.fire({ title: "Sucesso!", text: response.msg, icon: "success", timer: 2000 })
            .then(() => { window.location.href = '/home'; });

    } catch (error) {
        Swal.fire({ title: "Erro!", text: "Ocorreu um erro inesperado. Tente novamente.", icon: "error", timer: 3000 });
        console.error(error);
    }
});

btnCadastrar.addEventListener('click', async () => {
    try {
        const response = await new Requests()
            .setForm('formPreCadastro')
            .post('/precadastro');

        if (!response.status) {
            Swal.fire({ title: "Atenção!", text: response.msg, icon: "error", timer: 3000 });
            return;
        }

        Swal.fire({ title: "Sucesso!", text: response.msg, icon: "success", timer: 3000 })
            .then(() => { $('#pre-cadastro').modal('hide'); });

    } catch (error) {
        Swal.fire({ title: "Erro!", text: "Ocorreu um erro inesperado. Tente novamente.", icon: "error", timer: 3000 });
        console.error(error);
    }
});