#  nexus

**Facilitando a vida de quem estuda no ITEL – porque ninguém precisa sofrer sozinho!**
 
`nexus` é uma plataforma de apoio estudantil criada com Laravel, React, MySQL e Docker, voltada especialmente para os estudantes do **Instituto de Telecomunicações (ITEL)**. Aqui, os alunos encontram um verdadeiro **centro de sobrevivência acadêmica**: enunciados de exames anteriores, material didático organizado por disciplinas, fóruns de dúvidas, contato com veteranos solidários, e outras ferramentas para atravessar o curso com menos dor de cabeça.

---

##  Recursos disponíveis

-  **Banco de Enunciados**: provas, testes e exames dos anos anteriores por curso, disciplina e docente.
-  **Material Didático**: slides, livros, resumos e tutoriais enviados por professores ou veteranos.
-  **Garimpo**: lista de alunos e ex-alunos disponíveis para ajudar com explicações e dúvidas.
-  **Mentorias rápidas**: marque sessões com veteranos que já passaram pela mesma cadeira.
-  **Busca inteligente**: encontre conteúdos por nome da disciplina, docente ou área temática.
-  **Calendário acadêmico**: fique por dentro de avaliações, defesas, feriados e datas críticas.
-  **Fórum estudantil**: espaço aberto para tirar dúvidas, organizar grupos de estudo e desabar .
-  **Upload de conteúdos**: qualquer aluno pode contribuir com materiais, tudo passa por curadoria.

---

## ️ Tecnologias utilizadas

- [Laravel 11.x](https://laravel.com/)
- [MySQL 8.x](https://www.mysql.com/)
- [Docker + Docker Compose](https://www.docker.com/)
- React, CSS (Tailwind)

---

##  Executando localmente com Docker

### Pré-requisitos

- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/) instalados.

### Passos

```bash
# Clone o projeto
git clone https://github.com/seu-usuario/itel-facil.git
cd itel-facil

# Copie o .env de exemplo
cp .env.example .env

# Gere a chave da aplicação
docker run --rm -v $(pwd):/app laravelsail/php82-composer:latest bash -c "cd /app && composer install && php artisan key:generate"

# Suba os containers
docker-compose up -d

# Execute as migrations
docker exec -it itel-facil-app php artisan migrate --seed
```
---

## ‍ Como contribuir

Quer somar? Toda ajuda é bem-vinda!

1. Faça um fork do repositório
2. Crie uma branch com sua feature:

    ```bash
    git checkout -b minha-feature
    ```

3. Faça o commit das suas alterações:

    ```bash
    git commit -m 'Adiciona nova funcionalidade'
    ```

4. Faça o push para a branch:

    ```bash
    git push origin minha-feature
    ```

5. Abra um Pull Request explicando bem sua contribuição

---

##  Apoie a causa

Se você já sofreu pra encontrar uma prova antiga, teve que estudar só com print,  
ou sentiu falta de alguém pra te explicar “aquela matéria do Manico”...  
esse projeto é pra você.

**Compartilhe. Contribua. E bora facilitar o ITEL.**