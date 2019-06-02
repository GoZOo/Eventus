document.addEventListener('DOMContentLoaded',
    () => {
        document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].removeAttribute('href')
        document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].style.cursor = 'pointer'
        document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].addEventListener('click',
            () => {
                document.getElementById('succes-copy-ics').style.display = 'block'
                let dummy = document.createElement('input');
                document.body.appendChild(dummy);
                dummy.setAttribute('value', document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[0]);
                dummy.select();
                document.execCommand('copy');
                document.body.removeChild(dummy);
            }
        )
    }
)