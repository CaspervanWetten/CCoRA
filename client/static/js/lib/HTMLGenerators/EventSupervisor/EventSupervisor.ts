/// <reference path='./index.ts'/>
interface IEventSupervisor<S extends HTMLElement, T extends HTMLGenerator<S>>
{
    Generator : T;
    Register();
}