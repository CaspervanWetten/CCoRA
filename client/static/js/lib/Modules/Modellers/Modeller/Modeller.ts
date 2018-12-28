/// <reference path='./index.ts'/>
/// <reference path='../../../HTMLGenerators/EventSupervisor/index.ts'/>

class Modeller<T extends Drawer>{ 
    public Drawer               : T;
    protected MouseIsDown       : boolean;
    protected LastDownTarget    : EventTarget | undefined;

    public constructor(drawer : T)
    {
        this.Drawer         = drawer;
        this.MouseIsDown    = false;
        this.LastDownTarget = undefined;

        this.Register();
    }

    public Register()
    {
        let element = this.Drawer.Render();

        window.addEventListener('mousedown', (e)=>{
            this.LastDownTarget = e.target;
        });

        element.addEventListener("mousedown", (e) => {
            this.MouseIsDown = true;
        });
        
        element.addEventListener("mouseup", (e) => {
            this.MouseIsDown = false;
        });
    }
}