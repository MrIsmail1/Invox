import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
  types: [
    {
      type: 'success',
      duration: 3000,
      position: {
        x: 'right',
        y: 'top',
      },
      background: 'green',
    },
    {
      type: 'error',
      duration: 3000,
      position: {
        x: 'right',
        y: 'top',
      },
      background: 'red',
    },
  ]
});

export function successNotification() {
  notyf.success(message);
}

export function errorNotification() {
  notyf.error(message);
}