import './styles/app.scss';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.querySelectorAll('.collection-add').forEach((button) => {
  const collectionHolder = document.getElementById(button.dataset.target);
  const prototype = collectionHolder.dataset.prototype;
  button.addEventListener('click', () => {
    const index = parseInt(collectionHolder.dataset.index);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = prototype.replace(/__name__/g, index);
    wrapper.querySelector('.collection-remove').addEventListener('click', () => {
      wrapper.remove();
    });
    collectionHolder.appendChild(wrapper);
    collectionHolder.dataset.index = index + 1;
  });
});

