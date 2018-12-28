/// <reference path='./SplitContainer.ts'/>
/// <reference path='../../EventSupervisor/EventSupervisor.ts'/>

class SplitContainerSupervisor implements IEventSupervisor<HTMLElement, SplitContainer>
{
    public Generator : SplitContainer;
    
    protected LastDownTarget : EventTarget | undefined;
    protected MouseDown      : boolean;

    public constructor(generator : SplitContainer)
    {
        this.Generator = generator;

        this.LastDownTarget = undefined;
        this.MouseDown      = false;
    }
    
    public Register()
    {
        let element = this.Generator.Render();
        element.addEventListener("mousedown", (e)=>{
            this.MouseDown = true;            
            this.LastDownTarget = e.target;
        });
        element.addEventListener("mousemove", (e)=>{
            if(this.MouseDown && this.LastDownTarget == this.Generator.GetDivider()) {
                this.Generator.MoveDivider(e.clientX);
            }
        });
        element.addEventListener("mouseup", (e) => {
            this.MouseDown = false;
            this.LastDownTarget = undefined;
        });
    }
}