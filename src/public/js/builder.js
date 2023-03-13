export function buildQuestion(data) {
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

export function buildResults(data) {
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