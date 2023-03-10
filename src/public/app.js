"use strict";

let container = document.getElementById('container');
let deleteButton = document.getElementById('delete-button');
let nextButton = document.getElementById('next-button');

var loadedProgress = null;
let currentDetails = {};

const apiUrl = 'api/v1/';

async function getData(url) {
    const response = await fetch(url, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });
    const responseData = await response.json()
    return responseData;
}

async function updateData(url, data) {

    const response = await fetch(url, {
        method: 'POST',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ progress: data })
    });

    const responseData = await response.json();
    return JSON.parse(responseData);
}

async function deleteData(url) {
    const response = await fetch(url, {
        method: 'DELETE',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    });
    return true;
}

async function getQuestion(id = '1') {
    const url = apiUrl + 'questions/' + id;
    let responseObject = await getData(url);
    return responseObject;
}

async function getAnswers() {
    const url = apiUrl + 'questions';
    let responseObject = await getData(url);
    return responseObject;

}

async function getProgress() {
    const url = apiUrl + 'saved';
    let responseObject = await getData(url);
    let parsedObject = JSON.parse(responseObject)
    return parsedObject == null ? null : parsedObject.progress;
}

async function resetProgress() {
    const url = apiUrl + 'saved';
    let responseObject = await deleteData(url);
    loadedProgress = null;
    currentDetails = {};
    nextQuestion();
}

async function updateProgress(data) {
    const url = apiUrl + 'saved';
    let responseObject = await updateData(url, data);

    return responseObject;
}


async function saveProgress() {
    let selection = container.querySelector('input:checked') == null;

    if (selection) {
        console.log('Nothing checked');
        return false;
    }


    let value = selection ? null : container.querySelector('input:checked').value;
    if (loadedProgress == null) {
        loadedProgress = [];
    }
    loadedProgress[currentDetails.id] = value;

    if (await updateProgress(loadedProgress) == loadedProgress) {
        console.log('happened');
    }


    nextQuestion();
}

function clearTable() {
    container.innerHTML = '';
}

async function nextQuestion() {
    clearTable();
    init();
}

async function showResults() {
    let correctAnswers = await getAnswers();
    let result = 0;
    let details = [];
    for (let i = 0; i < loadedProgress.length-1; i++) {
        let obj = {
            id: correctAnswers[i].id
        };
        if(loadedProgress[i+1] == correctAnswers[i].correct_answer){
            obj.correct = true;
            result += 1;
        } else {
            obj.correct = false;
        }
        details.push(obj);
    }
    details.result = result;
    container.append(buildResults(details));
}

function buildQuestion(data) {
    let div = document.createElement('div');
    let h1 = document.createElement('h1');
    h1.classList.add('text-lg', 'font-bold', 'text-center');

    let pageSpan = document.createElement('span');
    pageSpan.id = 'page-number';
    pageSpan.innerText = data.id + '.';
    let questionSpan = document.createElement('span');
    questionSpan.classList.add('ml-1');
    questionSpan.innerText = data.question;

    h1.append(pageSpan, questionSpan);

    div.append(h1);

    div.append(document.createElement('hr'));

    let ulDiv = document.createElement('div');
    ulDiv.classList.add('my-4', 'text-center', 'mx-auto');
    div.append(ulDiv);

    let ul = document.createElement('ul');
    ul.classList.add('space-y-4', 'mx-auto');
    ulDiv.append(ul);

    data.choices.forEach((element, index) => {
        index += 1;
        let li = document.createElement('li');
        ul.append(li);

        let input = document.createElement('input');
        input.classList.add('p-2', 'mx-2', 'form-radio');
        input.type = 'radio';
        input.value = index;
        input.id = 'answer-' + index;
        input.name = 'answer-radio';

        let label = document.createElement('label');
        label.htmlFor = 'answer-' + index;
        label.innerText = element;

        li.append(input, label);
    });

    return div;
}

function buildResults(data) {
    let div = document.createElement('div');
    div.classList.add('my-4', 'text-center', 'mx-auto');

    let ul = document.createElement('ul');
    ul.classList.add('space-y-4', 'mx-auto');

    div.append(ul);

    data.forEach(element => {
        let li = document.createElement('li');
        li.innerText = 'Question ' + element.id + ': ' + ((element.correct) ? 'correct' : 'incorrect');
        ul.append(li);
    });

    let h1 = document.createElement('h1');
    h1.classList.add('text-lg', 'font-bold', 'text-center', 'my-4');
    h1.textContent = 'Result: ' + data.result;

    div.append(h1);

    return div;
}

const init = async function () {
    getProgress().then(async (progress) => {
        let data;
        if (progress === null) {
            data = await getQuestion(1);
        } else {
            loadedProgress = progress;
            data = await getQuestion(loadedProgress.length);
        }

        if (data.status == false) {
            showResults();
            return;
        }
        container.append(buildQuestion(data));
        currentDetails = data;
    });
}

window.addEventListener('DOMContentLoaded', () => {
    init();
});

nextButton.addEventListener('click', () => {
    saveProgress(); 
});

deleteButton.addEventListener('click', () => {
    resetProgress();
});



