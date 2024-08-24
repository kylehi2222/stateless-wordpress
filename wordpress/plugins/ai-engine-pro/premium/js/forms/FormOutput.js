const { useEffect, useRef, useState } = wp.element;

import ReplyActions from '@app/components/ReplyActions';

const FormOutput = (props) => {
  // eslint-disable-next-line no-unused-vars
  const { system, params, theme } = props;
  const { id, copyButton, className } = params;
  const baseClass = 'mwai-form-field-output';
  const classStr = `${baseClass}${className ? ' ' + className : ''}`;
  const [divContent, setDivContent] = useState(() =>
    divRef?.current?.textContent ? divRef.current.textContent : ''
  );
  const divRef = useRef(null);

  useEffect(() => {
    const observer = new MutationObserver((mutationsList) => {
      for (const mutation of mutationsList) {
        if (mutation.type === 'childList') {
          setDivContent(divRef.current.innerText);
        }
      }
    });
    if (divRef.current) {
      observer.observe(divRef.current, { childList: true, subtree: true });
    }
    return () => observer.disconnect();
  }, []);

  return (
    <div style={{ position: 'relative' }}>
      <ReplyActions content={divContent} enabled={copyButton}>
        <div id={id} ref={divRef} className={classStr}>
        </div>
      </ReplyActions>
    </div>
  );
};

export default FormOutput;
